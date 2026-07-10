from __future__ import annotations

import importlib.util
import tempfile
import unittest
from pathlib import Path
from typing import Any


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
            ["hea-lth-ops"],
            1024,
        )
        self.assertNotIn("__DEPLOY_TOKEN__", code)
        self.assertNotIn("__ALLOWED_SLUGS_B64__", code)
        self.assertNotIn("__MAX_PACKAGE_BYTES__", code)
        self.assertNotIn("<?php", code[:20])

    def test_multipart_contains_package_bytes_and_closing_boundary(self) -> None:
        with tempfile.TemporaryDirectory() as directory:
            package = Path(directory) / "release.zip"
            package.write_bytes(b"PK\x03\x04test")
            body, content_type = deploy.multipart({"slug": "hea-lth-ops"}, "package", package)
        boundary = content_type.split("boundary=", 1)[1]
        self.assertIn(b"hea-lth-ops", body)
        self.assertIn(b"PK\x03\x04test", body)
        self.assertTrue(body.endswith(f"--{boundary}--\r\n".encode()))


class VerificationTests(unittest.TestCase):
    PACKAGE = {"slug": "hea-lth-ops", "healthcheck_path": "/wp-json/hea-lth-ops/v1/healthcheck"}

    def test_health_requires_matching_deployment_id(self) -> None:
        client = FakeClient(
            [(200, {"status": "ok", "component": "hea-lth-ops", "version": "0.1.0", "deployment_id": "wrong"})]
        )
        with self.assertRaises(deploy.DeploymentError):
            deploy.verify_health(client, self.PACKAGE, "0.1.0", "deploy-correct")

    def test_health_accepts_exact_release_identity(self) -> None:
        response = {
            "status": "ok",
            "component": "hea-lth-ops",
            "version": "0.1.0",
            "deployment_id": "deploy-correct",
        }
        client = FakeClient([(200, response)])
        self.assertEqual(
            deploy.verify_health(client, self.PACKAGE, "0.1.0", "deploy-correct"),
            response,
        )

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


if __name__ == "__main__":
    unittest.main()
