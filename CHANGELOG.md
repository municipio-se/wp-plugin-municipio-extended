# Changelog

[Svensk version](CHANGELOG.sv.md)

## 2025.12.12 – 2026-06-01

- **Text module colors** – Added opt-in color presets that resolve against the
  active Municipio color palette and can be adjusted through project-level
  filters.
  [PR #11](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/11).
- **Iframe accessibility** – Preserved iframe titles when Modularity iframe
  modules and raw content iframes are rendered through consent-aware WSTG
  embeds.

## 2025.12.11 – 2026-05-15

- Fixed offset-less job date parsing so date strings are interpreted in the
  WordPress site timezone. This prevents Visma Recruit application deadlines
  from shifting to the next calendar day when shown on sites such as
  Europe/Stockholm.
  [PR #9](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/9).
- Added configurable Elasticsearch search error logging. Logging respects
  `WP_DEBUG` and `WP_DEBUG_LOG` by default and can be controlled with the
  `mx_search_error_logging_enabled` and `mx_search_error_log_path` filters.
  [PR #8](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/8).
- Thank you @michaelclaesson for contributing!

## 2025.12.10 – 2026-05-08

- Rebuilt distributed CSS assets so the horizontal overflow fix from `2025.12.9`
  is included in packaged assets.

## 2025.12.9 – 2026-05-08

- Changed global horizontal overflow handling from `overflow-x: hidden` to
  `overflow-x: clip` to prevent unwanted horizontal scrolling more reliably.

## 2025.12.8 – 2026-05-05

- Fixed the `attachment_updated` hook callback signature to match the three
  arguments WordPress provides, preventing `ArgumentCountError` fatals during
  migrated or imported attachment updates while preserving transient cache
  invalidation.

## 2025.12.7 – 2026-03-03

- Fixed a fatal error when enqueueing the Municipio theme Material Symbols
  editor font before the Municipio theme constants are available.

## 2025.12.6 – 2026-03-02

- Allowed editors to access the WordPress admin dashboard landing page after
  login.
  [PR #123](https://github.com/municipio-lts/wp-plugin-municipio-extended-2024/pull/123).

## 2025.12.5 – 2026-02-17

- Fixed WordPress gallery styling so configured gallery column counts are
  respected.

## 2025.12.4 – 2026-02-02

- Fixed custom 404 handling for unmatched paths by forcing 404 state only when
  no rewrite rule, posts archive, or valid page matches, then rendering the
  configured custom 404 page as a regular page.

## 2025.12.3 – 2026-01-15

- Fixed navigation modules using menu source by passing menu depth and parent
  arguments in the correct order.

## 2025.12.2 – 2026-01-14

- Fixed Nested Pages-backed navigation so it checks associated menu items until
  it finds the related item that actually has children.

## 2025.12.1 – 2026-01-12

- Added the `mx_mod_navigation_use_nested_pages` filter so child and sibling
  navigation modules can use the Nested Pages menu instead of the page tree.
- Exposed reusable menu item loading through
  `ModNavigation::getMenuItemsByMenu()`.

## 2025.12.0 – 2025-12-30

- Added a root `LICENSE` file.
- Updated Composer package license metadata from `AGPL-3.0` to
  `GPL-2.0-or-later`.
- Updated the README description to position the plugin as part of Municipio
  LTS.
