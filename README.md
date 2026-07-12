# Municipio Extended

[Svensk version](README.sv.md)

Part of [Municipio LTS](https://github.com/municipio-se/municipio-lts). Adds
compatibility fixes, modules, settings, admin tools, and integration glue around
Municipio, Modularity, ACF, Kirki, ElasticPress, and selected WordPress plugins.

## Requirements

Municipio Extended requires a Municipio LTS installation running PHP `8.1+`. The
active theme must be `municipio/wp-theme-municipio` on the same LTS version line
as the rest of the stack.

The required plugins are `municipio/wp-plugin-hbg-component-library`,
`municipio/wp-plugin-modularity`, `wpackagist-plugin/advanced-custom-fields`,
and `wpackagist-plugin/kirki`.

## Features

- **Custom post types and fields** – Alerts, async jobs, module navigation
  fields, page appearance fields, page navigation fields, archive meta fields,
  hidden title fields, search keywords, and event form fields.
- **Modularity extensions** – Custom templates, MXUI card/segment output, module
  groups, module editor UI, module wrappers, manual input segments, files lists,
  iframe/video/contacts/timeline templates, and posts module filtering.
- **Search** – Custom AJAX search endpoint, ElasticPress query shaping, result
  mapping, highlights, post type boosts, date decay, content type metadata,
  keyword indexing, and optional search error logging since `v2025.12.11`.
- **Theme/customizer settings** – General title behavior, layout widths,
  typography, header variants, print button visibility, MXUI colors, fallback
  images, content layout, archive table display, and module group backgrounds.
- **Frontend behavior** – Custom 404 rendering since `v2025.12.4`, gallery
  column styling since `v2025.12.5`, horizontal overflow clipping since
  `v2025.12.9`, robots upload blocking, RSS subscription support, custom
  widgets, and article button navigation.
- **Media and assets** – Uploaded fonts, Material Symbols caching, local assets,
  editor styles, Tailwind helpers, icon models, image models, and attachment URL
  lookup caching.
- **Admin tools** – Theme mod export/import/clone, migrations UI, editor access
  simplification, hidden admin items, disabled metadata plugin, and optional
  advanced HTML restrictions.

## Compatibility and Fixes

- **WordPress** – Disables user enumeration REST endpoints, disables automatic
  image `sizes`, normalizes rewrite `with_front`, fixes attachment URL caches,
  and fixes `attachment_updated` callback arguments since `v2025.12.8`.
- **Municipio** – Adds view/controller/component paths, adjusts headers, sidebar
  classes, breadcrumbs, content areas, custom 404, print accessibility item,
  search form behavior, and template view data.
- **Modularity** – Adds module templates, editor UI, module groups, sidebar
  compatibility data, and module wrappers.
- **ACF/Kirki** – Adds local fields and customizer fields, loads Kirki dynamic
  styles in the editor, and guards Material Symbols editor font enqueueing since
  `v2025.12.7`.
- **ElasticPress** – Skips default query integration, prepares indexed metadata,
  and exposes search query/result filters.
- **Event Manager Integration** – Disables event hero overlay, adjusts taxonomy
  rewrite, event form options, and event metadata.
- **Redirection** – Allows editor-level access through the configured role,
  including dashboard access since `v2025.12.6`.
- **Activity Log** – Logs migration status changes when Activity Log is active.
- **Two Factor** – Enforces configured two-factor providers.
- **Tracking GDPR** – Moves Municipio-specific consent dialog styling and script
  attribute handling.

## Admin Tools and Migrations

- **Migration files** – Add one-time migration files in `migrations/`.
- **Timeouts** – Use `mx_migration_breakpoint()` in repeat-safe migrations to
  avoid PHP timeouts.
- **Logging** – Use `mx_migration_progress_log()`, `mx_migration_error_log()`,
  `mx_migration_finish_log()`, and `mx_migration_halt_log()` for migration logs.
- **Status** – View status in WP Admin under Tools -> Migrations.
- **Example** – See `migrations/replace-mod-files1.php`.

## Search and Indexing

- **Endpoint** – `wp_ajax_mx_search` and `wp_ajax_nopriv_mx_search`.
- **Indexed metadata** – `content_type`, `content_type_formatted`, plain text
  content, and `search_keywords`.
- **Ranking** – Field matching, phrase boosts, keyword boosts, post type boosts,
  and optional date decay.
- **Result mapping** – Title, excerpt, URL, image, date, type, and score.
- **Error logging** – Disabled/enabled through WordPress debug settings by
  default and filterable since `v2025.12.11`.
- **Job date strings** – Strings without timezone are parsed in the WordPress
  timezone since `v2025.12.11`.

## Modularity and Theme Behavior

- **Navigation modules** – Support children, siblings, manual items, icons,
  descriptions, and hide-if-empty logic. Grid navigation backed directly by a
  WordPress menu has moved to the standalone Modularity Navigation plugin on the
  `decommission` branch.
- **Nested Pages** – Can back child/sibling navigation with
  `mx_mod_navigation_use_nested_pages` since `v2025.12.1`.
- **Module groups** – Can group modules by background and ignore unsupported
  sidebars.
- **Manual input modules** – Support segment output and image aspect ratio
  choices.
- **Text module color presets** – Disabled by default since `v2025.12.12`. Set
  `MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS` to `true` or return `true`
  from `mx_mod_text_use_color_presets` to replace the legacy text box color
  picker with a preset dropdown backed by Municipio palette colors. The filter
  runs after the constant default, so project code can override it in either
  direction.
- **Posts modules** – Support taxonomy filtering, search metadata, and archive
  table fields. On the `decommission` branch, the mixed template is instead
  owned by the standalone Modularity Posts plugin.
- **Gallery column CSS** – Respects WordPress gallery column classes since
  `v2025.12.5`.

## Hook Reference

### Model and Rendering

`apply_filters( 'mx/model/namespaces', string[] $namespaces, string $class, array $args )`
Change model namespaces searched by `mx_get_model()`.

`apply_filters( 'mx/model/class', class-string|null $full_class, string $class )`
Override the resolved model class.

`apply_filters( 'mx/module_wrapper_attrs', array $attrs, array $args, string $postType, int $postId )`
Add attributes to Modularity module wrappers.

`apply_filters( 'mx/module/current_post', WP_Post|null $post, MxModule $module )`
Override the post a module is rendered in the context of. Defaults to the global
`$post`, which is empty outside the loop (e.g. REST requests).

`apply_filters( 'mxui/debug_enabled', bool $enabled )` Enable MXUI debug output.

### Component Modifiers

`apply_filters( 'ComponentLibrary/Component/Modifier', array $modifiers, mixed $context )`
Add global Component Library modifiers.

`apply_filters( 'ComponentLibrary/Component/{Component}/Modifier', array $modifiers, mixed $context )`
Add modifiers for one component class.

`apply_filters( 'ComponentLibrary/Component/Icon/AltText', array $alt_text )`
Override icon alt text map.

`apply_filters( 'ComponentLibrary/Component/Icon/AltTextPrefix', string $prefix )`
Override icon alt text prefix.

`apply_filters( 'ComponentLibrary/Component/Icon/AltTextUndefined', string $alt_text )`
Override fallback icon alt text.

### Navigation

`apply_filters( 'mx_mod_navigation_fields', array $fields )` Modify ACF fields
for the navigation module.

`apply_filters( 'mx_mod_navigation_use_nested_pages', bool $use_np, WP_Post $post, string $source, string $slug, int $id )`
Use Nested Pages menu data for children/siblings navigation. since `v2025.12.1`

`apply_filters( 'mx/mod_navigation/hide_if_empty', bool $hide, string $slug, int $id, array $data )`
Override whether empty navigation modules are hidden.

### Text Modules

`apply_filters( 'mx_mod_text_use_color_presets', bool $enabled )`

Enable the preset dropdown for text module box colors. Defaults to `false`, or
to the value of `MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS` when the
constant is defined. since `v2025.12.12`

`apply_filters( 'mx_mod_text_box_color_presets', array $presets )`

Change the available text module color presets, including labels and resolved
color values. since `v2025.12.12`

### Search

`apply_filters( 'mx_search_es_query', array $query, array $data, array $settings_post_types )`
Modify the base Elasticsearch bool query.

`apply_filters( 'mx_search_boosted_post_types', array $boosted_post_types, array $data )`
Change post type boost weights.

`apply_filters( 'mx_search_boosted_post_type_functions', array $boosted_post_type_functions, array $data )`
Change generated boost functions.

`apply_filters( 'mx_search_decaying_post_types', array $decaying_post_types, array $data )`
Change post types using date decay.

`apply_filters( 'mx_search_decaying_post_type_functions', array $decaying_post_type_functions, array $data )`
Change generated date decay functions.

`apply_filters( 'mx_search_es_function_score', array $function_score, array $data )`
Modify the Elasticsearch `function_score` query.

`apply_filters( 'mx_search_es_body', array $es_body, array $data )` Modify the
final Elasticsearch request body.

`apply_filters( 'mx_search_hit_source_mapping', array $hit_source_mapping, array $es_body, array $data )`
Change Elasticsearch hit-to-result mapping callbacks.

`apply_filters( 'mx_search_es_hit', array $transformed_hit, array $hit, array $es_results, array $es_body, array $data )`
Modify one transformed search hit.

`apply_filters( 'mx_search_results', array $results, array $es_results )` Modify
the final AJAX search response.

`apply_filters( 'mx_search_error_logging_enabled', bool $enabled )` Enable or
disable search error logging. since `v2025.12.11`

`apply_filters( 'mx_search_error_log_path', string $log_path )` Change the
search error log file path. since `v2025.12.11`

`apply_filters( 'mx_search_post_content_type', string $content_type, array $post_args, int $post_id )`
Change indexed content type.

`apply_filters( 'mx_search_post_content_type_formatted', string $label, array $post_args, int $post_id )`
Change indexed content type label.

### Archives, Posts, and Media

`apply_filters( 'mx/meta_field/label', string $label, string $meta_field )`
Change archive meta field labels.

`apply_filters( 'mx/meta_field/display_value', mixed $value, string $field )`
Change archive meta field display values.

`apply_filters( 'mx_post_types_with_front', string[] $post_types )` Keep
`with_front` for selected post type rewrites.

`apply_filters( 'mx_taxonomies_with_front', string[] $taxonomies )` Keep
`with_front` for selected taxonomy rewrites.

`apply_filters( 'mx_materialsymbols_cache_path', string $path )` Change Material
Symbols cache directory.

`apply_filters( 'mx_materialsymbols_cache_url', string $url )` Change Material
Symbols cache URL.

`apply_filters( 'mx_should_ignore_module_group_backgrounds', bool $ignore_backgrounds, string $sidebar, mixed $context, array $visible_sidebars )`
Override module group background support per sidebar/context.

## Development and Contribution

Read more about how to contribute in the
[CONTRIBUTING.md file](CONTRIBUTING.md).
