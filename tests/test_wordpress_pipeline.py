from __future__ import annotations

import importlib.util
import tempfile
import unittest
from pathlib import Path
from typing import Any
from unittest.mock import patch


REPO = Path(__file__).resolve().parents[1]
SPEC = importlib.util.spec_from_file_location(
    "deploy_wordpress", REPO / "scripts" / "deploy-wordpress.py"
)
assert SPEC and SPEC.loader
deploy = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(deploy)


class FakeClient:
    def __init__(self, responses: list[Any]):
        self.responses = list(responses)
        self.calls: list[tuple[str, str, dict[str, Any]]] = []

    def request(self, method: str, path: str, **kwargs: Any) -> tuple[int, Any]:
        self.calls.append((method, path, kwargs))
        if not self.responses:
            raise AssertionError(f"Unexpected request: {method} {path}")
        response = self.responses.pop(0)
        if isinstance(response, Exception):
            raise response
        return response


class RouteRenderingTests(unittest.TestCase):
    def test_route_template_is_fully_rendered(self) -> None:
        code = deploy.render_route(
            REPO / "deploy" / "agentdeploy-route.php",
            "x" * 64,
            ["hea-lth-platform-core", "hea-lth-portal", "hea-lth-portal-child"],
            ["hea-lth-portal-child"],
            1024,
        )
        self.assertNotIn("__DEPLOY_TOKEN__", code)
        self.assertNotIn("__ALLOWED_SLUGS_B64__", code)
        self.assertNotIn("__ACTIVATABLE_THEME_SLUGS_B64__", code)
        self.assertNotIn("__MAX_PACKAGE_BYTES__", code)
        self.assertNotIn("<?php", code[:20])
        self.assertIn("get_param('_agent_token')", code)
        self.assertNotIn("get_header('x-hea-lth-deploy-token')", code)

    def test_route_creates_nested_rollback_directory_recursively(self) -> None:
        route = (REPO / "deploy" / "agentdeploy-route.php").read_text(encoding="utf-8")
        self.assertIn("wp_mkdir_p($path)", route)
        self.assertIn("hea_lth_agent_deploy_ensure_directory($backupRoot)", route)
        self.assertIn("'php_version' => PHP_VERSION", route)
        self.assertIn("update_option(", route)
        self.assertIn("hea_lth_agent_deploy_release_option_name", route)
        self.assertIn("switch_theme($previousStylesheet)", route)
        self.assertIn("deactivate_plugins((string) $state['plugin_file'], true)", route)
        self.assertNotIn("$wp_filesystem->mkdir($backupRoot", route)

    def test_multipart_contains_package_bytes_and_closing_boundary(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            package = Path(directory) / "release.zip"
            package.write_bytes(b"PK\x03\x04test")
            body, content_type = deploy.multipart({"slug": "hea-lth-ops"}, "package", package)
        boundary = content_type.split("boundary=", 1)[1]
        self.assertIn(b"hea-lth-ops", body)
        self.assertIn(b"PK\x03\x04test", body)
        self.assertTrue(body.endswith(f"--{boundary}--\r\n".encode()))


class ClientHeaderTests(unittest.TestCase):
    def test_client_uses_upress_waf_compatible_identity(self) -> None:
        captured: list[dict[str, Any]] = []

        def run_curl(command: list[str], **kwargs: Any) -> Any:
            config_path = Path(command[command.index("--config") + 1])
            response_path = Path(command[command.index("--output") + 1])
            captured.append(
                {
                    "command": command,
                    "config": config_path.read_text(encoding="utf-8"),
                    "kwargs": kwargs,
                }
            )
            response_path.write_bytes(b"{}")
            return deploy.subprocess.CompletedProcess(command, 0, stdout="200", stderr="")

        client = deploy.WordPressClient("https://example.test", "admin", "app-password")
        with patch.object(deploy.subprocess, "run", side_effect=run_curl):
            client.request("GET", "/wp-json/wp/v2/users/me")

        request = captured[0]
        self.assertIn("User-Agent: Mozilla/5.0", request["config"])
        self.assertNotIn("X-Hea-Lth-Deploy", request["config"])
        self.assertIn("Authorization: Basic ", request["config"])
        self.assertNotIn("Authorization", " ".join(request["command"]))
        self.assertEqual(request["kwargs"]["timeout"], 195)

    def test_client_rejects_header_newlines(self) -> None:
        with self.assertRaises(deploy.DeploymentError):
            deploy.WordPressClient._curl_config({"X-Test": "safe\r\nInjected: true"}, 180)


class VerificationTests(unittest.TestCase):
    PACKAGE = {"slug": "hea-lth-platform-core", "healthcheck_path": "/wp-json/hea-lth-platform/v1/healthcheck"}

    def test_health_requires_matching_deployment_id(self) -> None:
        client = FakeClient(
            [(200, {"status": "ok", "component": "hea-lth-platform-core", "version": "0.1.0", "deployment_id": "wrong"})]
        )
        with self.assertRaises(deploy.DeploymentError):
            deploy.verify_health(client, self.PACKAGE, "0.1.0", "deploy-correct")

    def test_health_accepts_exact_release_identity(self) -> None:
        response = {
            "status": "ok",
            "component": "hea-lth-platform-core",
            "version": "0.1.0",
            "deployment_id": "deploy-correct",
        }
        client = FakeClient([(200, response)])
        self.assertEqual(
            deploy.verify_health(client, self.PACKAGE, "0.1.0", "deploy-correct"),
            response,
        )

    def test_inactive_theme_package_uses_authenticated_theme_rest_verification(self) -> None:
        package = {"kind": "theme", "slug": "hea-lth-portal", "healthcheck_path": ""}
        client = FakeClient([(200, {"stylesheet": "hea-lth-portal", "version": "0.1.0", "status": "inactive"})])
        verification = deploy.verify_health(client, package, "0.1.0", "deploy-theme")
        self.assertEqual(verification["status"], "ok")
        self.assertEqual(verification["component"], "hea-lth-portal")
        self.assertEqual(verification["verification"], "authenticated_theme_rest")
        self.assertIn("/wp-json/wp/v2/themes/hea-lth-portal?context=edit", client.calls[0][1])

    def test_authenticated_cache_purge_uses_configured_endpoint(self) -> None:
        client = FakeClient([(204, None)])
        deploy.purge_public_cache(client, {"cache_purge_path": "/wp-json/ezcache/v1/cache"})
        self.assertEqual(client.calls[0][0], "DELETE")
        self.assertEqual(client.calls[0][1], "/wp-json/ezcache/v1/cache")
        self.assertEqual(client.calls[0][2]["expected"], (200, 204))

    def test_public_theme_surface_requires_configured_assets(self) -> None:
        package = {
            "public_verification_markers": [
                "/wp-content/themes/hea-lth-portal/",
                "/wp-content/themes/hea-lth-portal-child/style.css",
            ]
        }
        client = FakeClient(
            [
                (
                    200,
                    '<link href="/wp-content/themes/hea-lth-portal/assets/css/portal.css"><link href="/wp-content/themes/hea-lth-portal-child/style.css">',
                )
            ]
        )
        deploy.verify_public_theme_surface(client, package)
        self.assertEqual(client.calls[0][0], "GET")
        self.assertEqual(client.calls[0][1], "/")

    def test_public_theme_surface_rejects_stale_html(self) -> None:
        package = {"public_verification_markers": ["/wp-content/themes/hea-lth-portal/"]}
        client = FakeClient([(200, '<link href="/wp-content/themes/hello-elementor/style.css">')])
        with self.assertRaises(deploy.DeploymentError):
            deploy.verify_public_theme_surface(client, package)

    def test_rollback_of_first_install_requires_health_route_absence(self) -> None:
        client = FakeClient([(404, {"code": "rest_no_route"})])
        deploy.verify_rollback(client, self.PACKAGE, {"status": "rolled_back", "had_target": False, "version": ""})
        self.assertEqual(client.calls[0][2]["expected"], (404,))

    def test_rollback_of_upgrade_requires_restored_version(self) -> None:
        client = FakeClient([(200, {"status": "ok", "version": "0.0.9"})])
        deploy.verify_rollback(
            client,
            self.PACKAGE,
            {"status": "rolled_back", "had_target": True, "version": "0.0.9"},
        )

    def test_version_compare_handles_patch_versions(self) -> None:
        self.assertGreaterEqual(deploy.compare_dotted_versions("7.4.33", "7.4"), 0)
        self.assertLess(deploy.compare_dotted_versions("7.4.33", "8.1"), 0)

    def test_internal_rollback_can_be_verified_without_second_route_call(self) -> None:
        error = deploy.WordPressResponseError(
            "POST",
            "/wp-json/agentdeploy/v1/run",
            500,
            {
                "code": "agentdeploy_install_failed",
                "data": {
                    "status": 500,
                    "rolled_back": True,
                    "rollback": {"status": "rolled_back", "had_target": False, "version": ""},
                },
            },
        )
        self.assertEqual(
            deploy.extract_internal_rollback(error),
            {"status": "rolled_back", "had_target": False, "version": ""},
        )


class BootstrapTests(unittest.TestCase):
    def test_inactive_code_snippets_is_activated_without_reinstall(self) -> None:
        client = FakeClient(
            [
                deploy.DeploymentError("missing namespace"),
                (200, [{"plugin": "code-snippets/code-snippets", "status": "inactive", "name": "Code Snippets"}]),
                (200, {"plugin": "code-snippets/code-snippets", "status": "active"}),
                (200, {"schema": {}}),
            ]
        )
        deploy.ensure_code_snippets(client, bootstrap=True)
        self.assertEqual(client.calls[2][0], "POST")
        self.assertIn("/wp-json/wp/v2/plugins/code-snippets/code-snippets", client.calls[2][1])


class ThemePackageTests(unittest.TestCase):
    def test_theme_files_are_source_controlled(self) -> None:
        parent = REPO / "theme-src" / "hea-lth-portal"
        for relative in ["style.css", "functions.php", "header.php", "footer.php", "front-page.php", "theme.json"]:
            self.assertTrue((parent / relative).is_file(), relative)

    def test_theme_packages_use_live_php_floor_and_child_activation(self) -> None:
        config = deploy.json.loads((REPO / "deploy" / "wordpress-deploy.json").read_text(encoding="utf-8"))
        parent = next(item for item in config["packages"] if item["name"] == "hea-lth-portal")
        child = next(item for item in config["packages"] if item["name"] == "hea-lth-portal-child")
        self.assertEqual(parent["manifest"]["requires_php"], "7.4")
        self.assertEqual(parent["kind"], "theme")
        self.assertFalse(parent["activate"])
        self.assertTrue(child["activate"])
        self.assertEqual(child["manifest"]["requires_php"], "7.4")
        self.assertEqual(child["healthcheck_path"], "/wp-json/hea-lth-portal/v1/healthcheck")


if __name__ == "__main__":
    unittest.main()
