#!/usr/bin/env python3
"""Build and verify deterministic WordPress plugin or theme ZIP packages."""

from __future__ import annotations

import argparse
import datetime as dt
import fnmatch
import hashlib
import hmac
import json
import os
import re
import stat
import sys
import zipfile
from pathlib import Path, PurePosixPath
from typing import Any, Iterable


ZIP_EPOCH = (1980, 1, 1, 0, 0, 0)


def fail(message: str) -> "NoReturn":
    raise SystemExit(message)


def load_config(path: Path) -> dict[str, Any]:
    try:
        data = json.loads(path.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as exc:
        fail(f"Cannot read deployment config {path}: {exc}")
    if not isinstance(data, dict) or not isinstance(data.get("packages"), list):
        fail("Deployment config must contain a packages array.")
    return data


def select_package(config: dict[str, Any], name: str) -> dict[str, Any]:
    matches = [item for item in config["packages"] if item.get("name") == name]
    if len(matches) != 1:
        fail(f"Expected exactly one package named {name!r}; found {len(matches)}.")
    return matches[0]


def read_version(repo: Path, package: dict[str, Any]) -> str:
    version_path = repo / str(package["version_file"])
    text = version_path.read_text(encoding="utf-8")
    match = re.search(str(package["version_pattern"]), text, flags=re.MULTILINE)
    if not match:
        fail(f"Could not read a version from {version_path}.")
    version = match.group(1)

    constant_file = package.get("version_constant_file")
    constant_pattern = package.get("version_constant_pattern")
    if constant_file and constant_pattern:
        constant_text = (repo / str(constant_file)).read_text(encoding="utf-8")
        constant_match = re.search(str(constant_pattern), constant_text, flags=re.MULTILINE)
        if not constant_match:
            fail(f"Could not read the version constant from {constant_file}.")
        if constant_match.group(1) != version:
            fail(f"Version mismatch: header={version}, constant={constant_match.group(1)}.")
    return version


def matches(path: str, patterns: Iterable[str]) -> bool:
    candidate = PurePosixPath(path)
    for pattern in patterns:
        normalized = str(pattern).replace("\\", "/")
        if normalized in {"**", "**/*"}:
            return True
        if fnmatch.fnmatch(path, normalized) or candidate.match(normalized):
            return True
    return False


def package_files(repo: Path, package: dict[str, Any]) -> list[tuple[Path, str]]:
    source = (repo / str(package["source"])).resolve()
    if not source.is_dir() or repo.resolve() not in (source, *source.parents):
        fail(f"Package source is missing or outside the repository: {source}")

    includes = [str(value) for value in package.get("include", ["**/*"])]
    excludes = [str(value) for value in package.get("exclude", [])]
    selected: list[tuple[Path, str]] = []
    for file_path in sorted(source.rglob("*"), key=lambda item: item.as_posix().lower()):
        if not file_path.is_file():
            continue
        relative = file_path.relative_to(source).as_posix()
        if relative.startswith("../") or "\\" in relative:
            fail(f"Unsafe archive path: {relative}")
        if matches(relative, includes) and not matches(relative, excludes):
            selected.append((file_path, relative))

    required = str(package["main_file"]).replace("\\", "/")
    if not any(relative == required for _, relative in selected):
        fail(f"Required package file {required!r} was not selected.")
    if not selected:
        fail("Package contains no files.")
    return selected


def source_date() -> tuple[tuple[int, int, int, int, int, int], str]:
    raw = os.environ.get("SOURCE_DATE_EPOCH")
    if not raw:
        return ZIP_EPOCH, "1980-01-01T00:00:00Z"
    moment = dt.datetime.fromtimestamp(int(raw), tz=dt.timezone.utc).replace(microsecond=0)
    safe = max(moment, dt.datetime(1980, 1, 1, tzinfo=dt.timezone.utc))
    return (safe.year, safe.month, safe.day, safe.hour, safe.minute, safe.second), safe.isoformat().replace("+00:00", "Z")


def write_zip(output: Path, slug: str, files: list[tuple[Path, str]], timestamp: tuple[int, int, int, int, int, int]) -> str:
    output.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(output, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for file_path, relative in files:
            archive_name = f"{slug}/{relative}"
            info = zipfile.ZipInfo(archive_name, date_time=timestamp)
            info.compress_type = zipfile.ZIP_DEFLATED
            info.create_system = 3
            info.external_attr = (stat.S_IFREG | 0o644) << 16
            archive.writestr(info, file_path.read_bytes())
    digest = hashlib.sha256(output.read_bytes()).hexdigest()
    output.with_suffix(output.suffix + ".sha256").write_text(f"{digest}  {output.name}\n", encoding="ascii")
    return digest


def verify_zip(path: Path, package: dict[str, Any], expected_version: str, expected_sha: str | None = None) -> dict[str, Any]:
    if not path.is_file():
        fail(f"Package does not exist: {path}")
    actual_sha = hashlib.sha256(path.read_bytes()).hexdigest()
    if expected_sha and not hmac.compare_digest(actual_sha, expected_sha):
        fail("ZIP checksum does not match the expected SHA-256 digest.")

    slug = str(package["slug"])
    main_file = str(package["main_file"]).replace("\\", "/")
    required = f"{slug}/{main_file}"
    with zipfile.ZipFile(path) as archive:
        names = archive.namelist()
        if len(names) != len(set(names)):
            fail("ZIP contains duplicate paths.")
        for name in names:
            if "\\" in name or name.startswith("/") or ".." in PurePosixPath(name).parts:
                fail(f"ZIP contains unsafe path: {name}")
            if not name.startswith(f"{slug}/"):
                fail(f"ZIP contains more than one package root: {name}")
        if required not in names:
            fail(f"ZIP does not contain required file {required}.")
        main_text = archive.read(required).decode("utf-8-sig")
        if expected_version not in main_text:
            fail(f"ZIP main file does not contain expected version {expected_version}.")
    return {"sha256": actual_sha, "files": len(names), "bytes": path.stat().st_size}


def build_manifest(
    config: dict[str, Any], package: dict[str, Any], version: str, digest: str, built_at: str, output: Path
) -> None:
    metadata = dict(package.get("manifest", {}))
    distribution_base = os.environ.get("HEA_LTH_DISTRIBUTION_BASE_URL", "").rstrip("/")
    zip_name = f"{package['slug']}-{version}.zip"
    metadata.update(
        {
            "slug": package["slug"],
            "version": version,
            "download_url": f"{distribution_base}/{zip_name}" if distribution_base else "",
            "last_updated": built_at,
            "sha256": digest,
            "sections": {"changelog": f"Automated governed release {version}."},
        }
    )
    output.write_text(json.dumps(metadata, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    parser.add_argument("--config", default="deploy/wordpress-deploy.json")
    parser.add_argument("--package", required=True, dest="package_name")
    parser.add_argument("--dist", default="plugin-dist")
    parser.add_argument("--verify-only", type=Path)
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    repo = Path(__file__).resolve().parents[1]
    config = load_config(repo / args.config)
    package = select_package(config, args.package_name)
    version = read_version(repo, package)

    if args.verify_only:
        summary = verify_zip(args.verify_only.resolve(), package, version)
        print(json.dumps({"package": args.package_name, "version": version, **summary}, sort_keys=True))
        return 0

    files = package_files(repo, package)
    timestamp, built_at = source_date()
    dist = (repo / args.dist).resolve()
    zip_path = dist / f"{package['slug']}-{version}.zip"
    digest = write_zip(zip_path, str(package["slug"]), files, timestamp)
    summary = verify_zip(zip_path, package, version, digest)
    manifest = dist / f"{package['slug']}.json"
    build_manifest(config, package, version, digest, built_at, manifest)
    print(
        json.dumps(
            {
                "package": args.package_name,
                "kind": package["kind"],
                "slug": package["slug"],
                "version": version,
                "zip": str(zip_path),
                "manifest": str(manifest),
                **summary,
            },
            sort_keys=True,
        )
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
