# Contributing to Municipio Extended

Municipio Extended is part of the Municipio LTS stack. Contributions should
preserve compatibility with Municipio, Modularity, ACF/Kirki, ElasticPress, and
the related WordPress plugin integrations documented in the README.

## Local Setup

- **PHP dependencies** - Run `composer install`.
- **Frontend dependencies** - Run `pnpm install`.
- **Asset development** - Run `pnpm dev` to build and watch frontend assets.
- **Asset builds** - Always run `pnpm build` before committing changes. Built
  assets are included in the repository so that consumers can use the plugin
  immediately after installation and need to be kept updated.
- **Tests** - No dedicated automated test scripts are documented in this
  repository today.

## Repository Structure

- **`municipio-extended.php`** - Plugin bootstrap and autoload entrypoint.
- **`autoload/`** - Files here are automatically loaded by the plugin, just like
  files put in WordPress’ mu-plugins directory. Separate concerns into separate
  files. Subfolders are not allowed.
- **`psr-4/`** - PHP classes under the `MunicipioExtended\` namespace which are
  autoloaded by Composer.
- **`views/`** - Blade templates and partials either overriding core views or
  providing new ones.
- **`src/`** - TypeScript and CSS sources.
- **`dist/` and `static/materialsymbols/`** - Generated and distributed assets.
- **`migrations/`** - One-time migration scripts.
- **`languages/`** - Translation files.

## Development Guidelines

- **Existing patterns first** - Follow WordPress, Municipio, and local plugin
  patterns before introducing new abstractions.
- **Small scope** - Keep changes focused on the relevant feature, integration,
  fix, or migration.
- **Rationale near code** - Document important business or technical rationale
  close to the code when behavior is not obvious.
- **Public hooks** - Add WordPress-style PHPDoc for public plugin-owned hooks.
- **Bilingual docs** - Keep README and changelog language pairs structurally
  aligned when editing documentation.

## Migrations

- **Location** - Add one-time migrations in `migrations/`.
- **Safety** - Make migrations repeat-safe and idempotent.
- **Long runs** - Use `mx_migration_breakpoint()` for long-running migrations.
- **Logging** - Use `mx_migration_progress_log()`, `mx_migration_error_log()`,
  `mx_migration_halt_log()`, and `mx_migration_finish_log()` as needed.
- **Risk** - Explain data transformation risks and rollback considerations in
  the PR.
