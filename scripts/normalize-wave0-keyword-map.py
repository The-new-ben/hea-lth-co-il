#!/usr/bin/env python3
"""Normalize the immutable Wave 0 URL hypotheses to the SEO operator contract.

The source pack remains unchanged. Candidate pages are deliberately mapped to
`hold` until localized SERP, Semrush, GSC, backlink, and crawl gates approve
creation.
"""

from __future__ import annotations

import argparse
import csv
from pathlib import Path


OUTPUT_FIELDS = [
    "cluster",
    "primary_keyword",
    "secondary_keywords",
    "intent",
    "serp_page_type",
    "target_url",
    "url_state",
    "decision",
    "monetization_route",
    "medical_review_level",
    "serp_research_status",
    "semrush_status",
    "notes",
]


def normalize_url_state(value: str) -> str:
    normalized = value.strip().casefold()
    if "existing" in normalized:
        return "existing"
    if "new" in normalized:
        return "new"
    if "noindex" in normalized:
        return "noindex"
    return "legacy-review"


def normalize_decision(value: str) -> tuple[str, str]:
    normalized = value.strip().casefold()
    if normalized in {"keep-improve", "merge-redirect", "hold", "noindex"}:
        return normalized, ""
    if normalized == "create":
        return "create", ""
    if "create" in normalized:
        return (
            "hold",
            "Wave 0 source decision was a create candidate. Held until the URL "
            "inventory, localized SERP overlap, GSC, Semrush, backlink, and "
            "medical-governance gates pass.",
        )
    return "hold", f"Unrecognized Wave 0 decision {value!r}; held for review."


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("source_csv", type=Path)
    parser.add_argument("output_csv", type=Path)
    args = parser.parse_args()

    args.output_csv.parent.mkdir(parents=True, exist_ok=True)
    seen_keywords: set[str] = set()
    seen_urls: set[str] = set()
    output_rows: list[dict[str, str]] = []

    with args.source_csv.open("r", encoding="utf-8-sig", newline="") as handle:
        reader = csv.DictReader(handle)
        for line_number, row in enumerate(reader, start=2):
            primary = (row.get("primary_keyword") or "").strip()
            target_url = (row.get("target_url") or "").strip()
            keyword_key = primary.casefold()
            if not primary:
                raise ValueError(f"Line {line_number}: empty primary keyword")
            if keyword_key in seen_keywords:
                raise ValueError(f"Line {line_number}: duplicate primary keyword {primary!r}")
            if target_url in seen_urls:
                raise ValueError(f"Line {line_number}: duplicate target URL {target_url!r}")
            if not target_url.startswith("/") or not target_url.endswith("/"):
                raise ValueError(f"Line {line_number}: invalid target URL {target_url!r}")
            seen_keywords.add(keyword_key)
            seen_urls.add(target_url)

            decision, decision_note = normalize_decision(
                row.get("keep_create_merge_hold_noindex") or ""
            )
            source_notes = (row.get("cannibalization_notes") or "").strip()
            notes = " ".join(part for part in (decision_note, source_notes) if part)
            output_rows.append(
                {
                    "cluster": (row.get("cluster") or "").strip(),
                    "primary_keyword": primary,
                    "secondary_keywords": (row.get("secondary_keywords") or "").strip(),
                    "intent": (row.get("intent") or "").strip(),
                    "serp_page_type": (row.get("serp_page_type") or "").strip(),
                    "target_url": target_url,
                    "url_state": normalize_url_state(row.get("existing_or_new") or ""),
                    "decision": decision,
                    "monetization_route": (row.get("monetization_route") or "").strip(),
                    "medical_review_level": (row.get("medical_review_level") or "").strip(),
                    "serp_research_status": (row.get("SERP_research_status") or "").strip(),
                    "semrush_status": (row.get("Semrush_status") or "").strip(),
                    "notes": notes,
                }
            )

    with args.output_csv.open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=OUTPUT_FIELDS, lineterminator="\n")
        writer.writeheader()
        writer.writerows(output_rows)

    print(f"Wrote {len(output_rows)} governed rows to {args.output_csv}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

