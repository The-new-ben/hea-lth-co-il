# Hea-lth theme architecture decisions

Date: 2026-07-10

## Decision

Build a new source-controlled WordPress theme named `Health Revenue` (`health-revenue`) instead of repairing the existing visual site.

## Why this architecture

Official WordPress documentation supports building themes through controlled theme files, template hierarchy, `theme.json`, template parts/patterns, and child-theme or custom-theme workflows. For Hea-lth, a custom theme is more appropriate than a cosmetic child-theme because the product is being rebuilt from zero: navigation, homepage, templates, funnels, provider discovery, and monetization.

References used:

- WordPress Theme Handbook: https://developer.wordpress.org/themes/
- WordPress Theme Structure: https://developer.wordpress.org/themes/core-concepts/theme-structure/
- WordPress Global Settings and Styles / `theme.json`: https://developer.wordpress.org/themes/global-settings-and-styles/
- WordPress Child Themes: https://developer.wordpress.org/themes/advanced-topics/child-themes/
- WordPress Interactivity API: https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/

## Implementation boundary

The theme owns presentation:

- header,
- footer,
- responsive navigation,
- homepage,
- page templates,
- design tokens,
- visual components,
- user-facing conversion layout.

The operations plugin owns platform services:

- deployment healthcheck,
- lead intake endpoint,
- private lead storage,
- future CRM/webhook bridge,
- future provider/account APIs.

This is not a patch layer on the old site. It is proper WordPress separation of concerns: theme for product surface, plugin for durable business logic.

## Activation gate

The theme can be packaged and installed automatically, but automatic public activation remains blocked until:

1. desktop and mobile screenshots are reviewed,
2. lead form is tested,
3. homepage content is checked for medical-safety boundaries,
4. top navigation is checked against URL/keyword governance,
5. existing powered URLs are not broken,
6. rollback path is ready.
