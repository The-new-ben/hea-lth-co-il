# hea-lth.co.il enterprise platform

WordPress delivery, governed SEO research, premium-health marketplace architecture, and revenue operations for `hea-lth.co.il`.

## Current control planes

- [WordPress agent deployment pipeline](docs/WORDPRESS_AGENT_DEPLOY_PIPELINE_2026-07-10.md)
- [Enterprise tooling registry and Slack operating model](docs/ENTERPRISE_TOOLING_REGISTRY_2026-07-10.md)
- [Wave 0 research baseline](research/hea-lth_research_pack_wave0_2026-07-10/00_README.md)
- [Codex Wave 0 intake and publishing gates](research/codex-wave0-intake-2026-07-10/README.md)
- [ChatGPT Pro continuation research prompt](docs/CHATGPT_CONTINUATION_MEGA_PROMPT_V2_2026-07-10.md)

## Deterministic WordPress package

```bash
python scripts/build-wordpress-package.py --package hea-lth-ops
python scripts/deploy-wordpress.py --package hea-lth-ops --dry-run
python -m unittest -v tests/test_wordpress_pipeline.py
```

Production deployment is automatic from the approved `main` branch after the GitHub `production` environment has `WP_BASE_URL`, `WP_USER`, and `WP_APP_PASSWORD` secrets.
