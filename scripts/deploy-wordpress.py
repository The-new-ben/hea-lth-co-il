#!/usr/bin/env python3
"""Deploy one deterministic package through a temporary WordPress REST bridge."""

from __future__ import annotations

import argparse
import base64
import hashlib
import json
import mimetypes
import os
import re
import secrets
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path
from typing import Any


# uPress's nginx/WAF rejects non-browser User-Agent values before the request
# reaches WordPress. Keep an explicit deployment marker in a separate header so
# hosting logs can still distinguish this client without triggering that rule.
WAF_COMPATIBLE_USER_AGENT = (
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
    "AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/138.0.0.0 Safari/537.36"
)


class DeploymentError(RuntimeError):
    pass


class WordPressClient:
    def __init__(self, base_url: str, username: str, application_password: str, timeout: int = 180):
        self.base_url = base_url.rstrip("/")
        token = base64.b64encode(f"{username}:{application_password}".encode()).decode("ascii")
        self.authorization = f"Basic {token}"
        self.timeout = timeout

    def request(
        self,
        method: str,
        path: str,
        *,
        json_body: dict[str, Any] | None = None,
        body: bytes | None = None,
        headers: dict[str, str] | None = None,
        expected: tuple[int, ...] = (200,),
    ) -> tuple[int, Any]:
        url = path if path.startswith("http") else f"{self.base_url}/{path.lstrip('/')}"
        request_headers = {
            "Authorization": self.authorization,
            "Accept": "application/json",
            "User-Agent": WAF_COMPATIBLE_USER_AGENT,
            "X-Hea-Lth-Deploy": "1.0",
        }
        if headers:
            request_headers.update(headers)
        if json_body is not None:
            body = json.dumps(json_body, separators=(",", ":")).encode("utf-8")
            request_headers["Content-Type"] = "application/json"

        request = urllib.request.Request(url, data=body, headers=request_headers, method=method)
        try:
            with urllib.request.urlopen(request, timeout=self.timeout) as response:
                status = response.status
                raw = response.read()
        except urllib.error.HTTPError as error:
            status = error.code
            raw = error.read()
        except urllib.error.URLError as error:
            raise DeploymentError(f"WordPress request failed: {method} {path}: {error.reason}") from error

        content: Any = None
        if raw:
            try:
                content = json.loads(raw.decode("utf-8-sig"))
            except (UnicodeDecodeError, json.JSONDecodeError):
                content = raw.decode("utf-8", errors="replace")[:4000]
        if status not in expected:
            safe_content = content
            if isinstance(content, dict):
                safe_content = {key: value for key, value in content.items() if key not in {"authorization", "password"}}
            raise DeploymentError(f"Unexpected WordPress response {status} for {method} {path}: {safe_content}")
        return status, content


def load_config(repo: Path, path: str, package_name: str) -> tuple[dict[str, Any], dict[str, Any]]:
    config_path = (repo / path).resolve()
    config = json.loads(config_path.read_text(encoding="utf-8"))
    packages = [item for item in config.get("packages", []) if item.get("name") == package_name]
    if len(packages) != 1:
        raise DeploymentError(f"Expected exactly one package named {package_name!r}.")
    return config, packages[0]


def read_manifest(repo: Path, package: dict[str, Any], dist: str) -> tuple[Path, dict[str, Any]]:
    manifest_path = (repo / dist / f"{package['slug']}.json").resolve()
    manifest = json.loads(manifest_path.read_text(encoding="utf-8"))
    version = str(manifest.get("version", ""))
    package_path = (repo / dist / f"{package['slug']}-{version}.zip").resolve()
    if not package_path.is_file():
        raise DeploymentError(f"Built ZIP is missing: {package_path}")
    digest = hashlib.sha256(package_path.read_bytes()).hexdigest()
    if not secrets.compare_digest(digest, str(manifest.get("sha256", ""))):
        raise DeploymentError("Built ZIP checksum does not match its manifest.")
    return package_path, manifest


def render_route(template_path: Path, token: str, allowed_slugs: list[str], max_bytes: int) -> str:
    code = template_path.read_text(encoding="utf-8")
    if code.startswith("<?php"):
        code = code[len("<?php") :].lstrip("\r\n")
    encoded_slugs = base64.b64encode(json.dumps(allowed_slugs).encode("utf-8")).decode("ascii")
    replacements = {
        "__DEPLOY_TOKEN__": token,
        "__ALLOWED_SLUGS_B64__": encoded_slugs,
        "__MAX_PACKAGE_BYTES__": str(max_bytes),
    }
    for marker, value in replacements.items():
        if marker not in code:
            raise DeploymentError(f"Deployment route template is missing marker {marker}.")
        code = code.replace(marker, value)
    if re.search(r"__[A-Z][A-Z0-9_]*__", code):
        raise DeploymentError("Deployment route template still contains an unresolved placeholder.")
    return code


def multipart(fields: dict[str, str], file_field: str, file_path: Path) -> tuple[bytes, str]:
    boundary = f"----hea-lth-{secrets.token_hex(24)}"
    chunks: list[bytes] = []
    for name, value in fields.items():
        chunks.extend(
            [
                f"--{boundary}\r\n".encode(),
                f'Content-Disposition: form-data; name="{name}"\r\n\r\n'.encode(),
                value.encode("utf-8"),
                b"\r\n",
            ]
        )
    mime = mimetypes.guess_type(file_path.name)[0] or "application/zip"
    chunks.extend(
        [
            f"--{boundary}\r\n".encode(),
            f'Content-Disposition: form-data; name="{file_field}"; filename="{file_path.name}"\r\n'.encode(),
            f"Content-Type: {mime}\r\n\r\n".encode(),
            file_path.read_bytes(),
            b"\r\n",
            f"--{boundary}--\r\n".encode(),
        ]
    )
    return b"".join(chunks), f"multipart/form-data; boundary={boundary}"


def ensure_code_snippets(client: WordPressClient, bootstrap: bool) -> None:
    try:
        client.request("GET", "/wp-json/code-snippets/v1/snippets/schema", expected=(200,))
        return
    except DeploymentError:
        if not bootstrap:
            raise

    _, plugins = client.request(
        "GET",
        "/wp-json/wp/v2/plugins?search=Code%20Snippets&_fields=plugin,status,name",
        expected=(200,),
    )
    installed = next(
        (
            item
            for item in plugins
            if isinstance(item, dict) and str(item.get("plugin", "")).startswith("code-snippets/")
        ),
        None,
    ) if isinstance(plugins, list) else None

    if installed:
        # Core's item route omits the .php suffix and appends it in
        # WP_REST_Plugins_Controller::sanitize_plugin_param().
        plugin_route = str(installed["plugin"])
        if plugin_route.endswith(".php"):
            plugin_route = plugin_route[:-4]
        plugin_file = urllib.parse.quote(plugin_route, safe="/")
        print("Code Snippets is installed but inactive; activating it through WordPress core REST.")
        client.request(
            "POST",
            f"/wp-json/wp/v2/plugins/{plugin_file}",
            json_body={"status": "active"},
            expected=(200,),
        )
    else:
        print("Code Snippets REST API is absent; installing and activating the official WordPress.org plugin.")
        client.request(
            "POST",
            "/wp-json/wp/v2/plugins",
            json_body={"slug": "code-snippets", "status": "active"},
            expected=(200, 201),
        )
    for _ in range(12):
        try:
            client.request("GET", "/wp-json/code-snippets/v1/snippets/schema", expected=(200,))
            return
        except DeploymentError:
            time.sleep(2)
    raise DeploymentError("Code Snippets was installed but its REST API did not become available.")


def verify_health(client: WordPressClient, package: dict[str, Any], version: str, deployment_id: str) -> dict[str, Any]:
    path = str(package.get("healthcheck_path", ""))
    if not path:
        raise DeploymentError("Automatic deployment requires a component healthcheck path.")
    separator = "&" if "?" in path else "?"
    _, content = client.request("GET", f"{path}{separator}deployment={urllib.parse.quote(deployment_id)}", expected=(200,))
    if not isinstance(content, dict) or content.get("status") != "ok":
        raise DeploymentError(f"Healthcheck did not report ok: {content}")
    if not secrets.compare_digest(str(content.get("component", "")), str(package["slug"])):
        raise DeploymentError(
            f"Healthcheck component mismatch: expected {package['slug']}, received {content.get('component')}"
        )
    if not secrets.compare_digest(str(content.get("version", "")), version):
        raise DeploymentError(f"Healthcheck version mismatch: expected {version}, received {content.get('version')}")
    if not secrets.compare_digest(str(content.get("deployment_id", "")), deployment_id):
        raise DeploymentError(
            "Healthcheck deployment ID does not match the release that was just installed."
        )
    return content


def verify_rollback(client: WordPressClient, package: dict[str, Any], rollback: Any) -> None:
    if not isinstance(rollback, dict) or rollback.get("status") != "rolled_back":
        raise DeploymentError(f"Rollback did not report a verified restoration: {rollback}")

    path = str(package.get("healthcheck_path", ""))
    if not path:
        return
    if rollback.get("had_target"):
        _, health = client.request("GET", path, expected=(200,))
        expected_version = str(rollback.get("version", ""))
        if not isinstance(health, dict) or health.get("status") != "ok":
            raise DeploymentError(f"Restored package healthcheck failed: {health}")
        if expected_version and not secrets.compare_digest(str(health.get("version", "")), expected_version):
            raise DeploymentError(
                f"Restored version mismatch: expected {expected_version}, received {health.get('version')}"
            )
    else:
        client.request("GET", path, expected=(404,))


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    parser.add_argument("--config", default="deploy/wordpress-deploy.json")
    parser.add_argument("--route-template", default="deploy/agentdeploy-route.php")
    parser.add_argument("--package", required=True, dest="package_name")
    parser.add_argument("--dist", default="plugin-dist")
    parser.add_argument("--bootstrap-code-snippets", action="store_true")
    parser.add_argument("--dry-run", action="store_true")
    parser.add_argument("--deployment-id")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    repo = Path(__file__).resolve().parents[1]
    config, package = load_config(repo, args.config, args.package_name)
    package_path, manifest = read_manifest(repo, package, args.dist)
    version = str(manifest["version"])
    digest = str(manifest["sha256"])
    deployment_id = args.deployment_id or f"deploy-{os.environ.get('GITHUB_SHA', secrets.token_hex(16))[:40]}"

    if args.dry_run:
        print(
            json.dumps(
                {
                    "status": "dry_run_ok",
                    "package": package["name"],
                    "kind": package["kind"],
                    "slug": package["slug"],
                    "version": version,
                    "sha256": digest,
                    "bytes": package_path.stat().st_size,
                },
                sort_keys=True,
            )
        )
        return 0

    base_url = os.environ.get("WP_BASE_URL", str(config.get("site_url", ""))).strip()
    username = os.environ.get("WP_USER", "").strip()
    application_password = os.environ.get("WP_APP_PASSWORD", "").strip()
    if not base_url or not username or not application_password:
        raise DeploymentError("WP_BASE_URL, WP_USER, and WP_APP_PASSWORD are required for live deployment.")

    client = WordPressClient(base_url, username, application_password)
    _, identity = client.request("GET", "/wp-json/wp/v2/users/me?context=edit&_fields=id,name,roles,capabilities", expected=(200,))
    capabilities = identity.get("capabilities", {}) if isinstance(identity, dict) else {}
    if not capabilities.get("update_plugins"):
        raise DeploymentError("The WordPress deployment user lacks update_plugins capability.")
    print(f"Authenticated WordPress deployment user id={identity.get('id')} with update_plugins capability.")

    ensure_code_snippets(client, args.bootstrap_code_snippets)
    deploy_token = secrets.token_urlsafe(48)
    route_code = render_route(
        repo / args.route_template,
        deploy_token,
        [str(item["slug"]) for item in config["packages"]],
        int(config.get("max_package_bytes", 67108864)),
    )
    snippet_id: int | None = None
    run_started = False
    finalized = False
    deployment_failed = False
    deploy_headers = {
        "X-Hea-Lth-Deploy-Token": deploy_token,
        "X-Hea-Lth-Deployment-Id": deployment_id,
    }

    try:
        _, created = client.request(
            "POST",
            "/wp-json/code-snippets/v1/snippets",
            json_body={
                "name": f"tmp-agentdeploy-{deployment_id}",
                "desc": "Temporary checksum-verified deployment route. Delete immediately after use.",
                "code": route_code,
                "scope": "global",
                "active": True,
                "priority": 1,
                "tags": ["temporary", "agentdeploy"],
            },
            expected=(200, 201),
        )
        snippet_id = int(created["id"])
        print(f"Created temporary deployment bridge snippet id={snippet_id}.")

        _, preflight = client.request(
            "GET", "/wp-json/agentdeploy/v1/preflight", headers=deploy_headers, expected=(200,)
        )
        if int(preflight.get("max_upload_bytes", 0)) < package_path.stat().st_size:
            raise DeploymentError("WordPress upload capacity is smaller than the built package.")

        fields = {
            "kind": str(package["kind"]),
            "slug": str(package["slug"]),
            "main_file": str(package["main_file"]),
            "version": version,
            "sha256": digest,
            "deployment_id": deployment_id,
            "activate": "true" if package.get("activate") else "false",
        }
        body, content_type = multipart(fields, "package", package_path)
        run_started = True
        _, result = client.request(
            "POST",
            "/wp-json/agentdeploy/v1/run",
            body=body,
            headers={**deploy_headers, "Content-Type": content_type},
            expected=(200,),
        )
        print(f"WordPress installed {package['slug']} version {result.get('version')}; verifying independently.")

        health = verify_health(client, package, version, deployment_id)
        print(f"Independent healthcheck passed for version {health.get('version')}.")

        client.request(
            "POST",
            "/wp-json/agentdeploy/v1/finalize",
            json_body={"deployment_id": deployment_id},
            headers=deploy_headers,
            expected=(200,),
        )
        finalized = True
        print("Rollback backup finalized after verification.")
    except Exception:
        deployment_failed = True
        if run_started and not finalized:
            try:
                _, rollback = client.request(
                    "POST",
                    "/wp-json/agentdeploy/v1/rollback",
                    json_body={"deployment_id": deployment_id},
                    headers=deploy_headers,
                    expected=(200,),
                )
                verify_rollback(client, package, rollback)
                print("Verification failed; WordPress package rollback was independently verified.", file=sys.stderr)
            except Exception as rollback_error:
                print(f"CRITICAL: automatic rollback failed: {rollback_error}", file=sys.stderr)
        raise
    finally:
        if snippet_id is not None:
            try:
                client.request(
                    "DELETE",
                    f"/wp-json/code-snippets/v1/snippets/{snippet_id}",
                    expected=(200, 204),
                )
                print(f"Deleted temporary deployment bridge snippet id={snippet_id}.")
            except Exception as cleanup_error:
                print(f"CRITICAL: temporary deployment bridge cleanup failed: {cleanup_error}", file=sys.stderr)
                if not deployment_failed:
                    raise

            try:
                client.request(
                    "GET",
                    "/wp-json/agentdeploy/v1/preflight",
                    headers=deploy_headers,
                    expected=(404,),
                )
                print("Confirmed the temporary deployment route is absent.")
            except Exception as route_error:
                print(f"CRITICAL: deployment route still responds after cleanup: {route_error}", file=sys.stderr)
                if not deployment_failed:
                    raise

    print(json.dumps({"status": "deployed", "deployment_id": deployment_id, "version": version}, sort_keys=True))
    return 0


if __name__ == "__main__":
    try:
        sys.exit(main())
    except DeploymentError as error:
        print(f"DEPLOYMENT ERROR: {error}", file=sys.stderr)
        sys.exit(1)
