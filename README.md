# plugin-dev-test

Test plugin of [wp-app-framework](https://github.com/saijsaij/wp-app-framework). It is not a product plugin: its block, hook, routes, templates and public interface exist to exercise the framework in a plugin, next to the test theme [theme-dev-test](https://github.com/saijsaij/theme-dev-test).

- Plugin slug (directory name): `plugin-dev-test`, main file `plugin-dev-test.php`
- Block namespace: `plugin-dev-test/`, REST namespace: `plugin-dev-test/v1`, template prefix `plugin_dev_test_`
- Requires WordPress 7.1 and PHP 8.5

## Interface for other consumers

- **Public API:** `Saij\PluginDevTest\Api\NoteApi` (`#[PublicApi]`), injected with `#[PluginApi('plugin-dev-test')] ?NoteApi $notes = null`.
- **Public template callables:** `plugin_dev_test_note_count()`, `plugin_dev_test_partial(name)` (HTML) and the HTML filter `plugin_dev_test_highlight`, for consumers that list `plugin-dev-test` in `extra.wp-app-framework.template-plugins`.
- **Overridable template:** `Twig/Overridable.twig`; a theme overrides it with `overrides/plugin-dev-test/Twig/Overridable.twig`.

The test theme uses all three; its `plugin.spec.js` checks them.

## Development

The framework is required from its GitHub repository as `dev-main`, like in the test theme. Clone the repositories side by side and develop with [wp-dev-env](https://github.com/saijsaij/wp-dev-env) with `WP_THEMES=theme-dev-test`, `WP_ACTIVE_THEME=theme-dev-test`, `WP_PLUGINS=plugin-dev-test` and `WP_PACKAGE_LINKS=saij/wp-app-framework=wp-app-framework`; its setup runs `composer install`, `npm ci` and `npm run build` for theme and plugin and activates the plugin.

```sh
git clone https://github.com/saijsaij/wp-app-framework.git
git clone https://github.com/saijsaij/theme-dev-test.git
git clone https://github.com/saijsaij/plugin-dev-test.git
```

## Release

`composer release` builds `dist/plugin-dev-test-<version>.zip` with the release pipeline of the framework (see [Release](https://saijsaij.github.io/wp-app-framework/next/build-and-release/release.html) in its documentation); the framework comes from the lock file, or from the local checkout with `-- --package-source=saij/wp-app-framework=../wp-app-framework`. A tag `v<version>` (= `Version` in `plugin-dev-test.php`) runs `.github/workflows/release.yml`: build, release test next to the release of the test theme named there (`with-theme`) with all browser tests, then the ZIP is attached to the GitHub release of the tag.

## Tests

All checks run with [wp-test-env](https://github.com/saijsaij/wp-test-env), checked out next to this repository, and must pass without errors or warnings. From this directory:

```sh
../wp-test-env/bin/tools -w plugin-dev-test composer build-manifest
../wp-test-env/bin/tools -w plugin-dev-test composer lint   # fix: composer fix
../wp-test-env/bin/tools -w plugin-dev-test npm run lint    # fix: npm run format
```

`build-manifest` must report

```
Manifest OK (plugin plugin-dev-test): 35 service candidates, 23 services, 2 hooks, 15 routes, 37 template functions, 1 template filters, 3 blocks, 1 CLI commands, 0 cron events, 0 cron tasks, 1 tables, 3 lifecycle methods. Nothing written (no --output).
Public: 1 APIs, 3 template functions and filters.
Assets: 2 static files, 0 image variants.
```

Browser tests (Playwright, `tests/e2e/`), WP-CLI tests (`tests/cli/run`) and lifecycle tests (`tests/lifecycle/run`) run in fresh WordPress instances of wp-test-env, described in `tests/dev.pools` (the checkouts of plugin and test theme) and `tests/release.pools` (the plugin's release ZIP, built first with the framework checkout, next to the release of the test theme that `with-theme` in `.github/workflows/release.yml` names, downloaded by `tests/download-with-theme`, as in the release workflow). From the wp-test-env directory:

```sh
bin/run ../plugin-dev-test/tests/dev.pools
bin/run ../theme-dev-test/tests/release.pools ../plugin-dev-test/tests/release.pools
```

| Pool | Instances | Runs |
|---|---|---|
| `pdt` | 2 | browser tests, the test theme active |
| `pdt-cli` | 1 | WP-CLI tests |
| `pdt-lifecycle` | 1 | lifecycle tests |
| `pdt-release`, `pdt-release-cli`, `pdt-release-lifecycle` | 2, 1, 1 | the same against the release ZIPs |

The browser tests need the test theme as active theme or as parent of the active child theme [theme-dev-test-child](https://github.com/saijsaij/theme-dev-test-child) (`requireTestTheme()` fails otherwise). Each Playwright worker has an instance of its own (`WP_BASE_URLS`, picked per worker in `tests/e2e/playwright.config.js`). The console guard in `tests/e2e/fixtures.js` is the same as in the test theme.

WP-CLI tests run the plugin's command next to those of the test theme with `bin/wp` of wp-test-env. The theme's command is checked only if the active theme offers it (`wp cli has-command`); otherwise the script prints a `skip:` line and goes on, like the browser tests branch on the plugin status. The release workflow runs them with `cli: true`.

Lifecycle tests deactivate, activate and uninstall the plugin with WP-CLI and check what the framework and the lifecycle methods did: the uninstallation removes the posts of the plugin's post type, its meta keys (also on posts of WordPress), its settings, its table, its cron events, its options and its cache directory, not its files (`--skip-delete`). The plugin is active again at the end; the pool gives them an instance of their own, since they delete all notes of the plugin.

Single specs or scripts against an instance of your own, from the wp-test-env directory:

```sh
bin/up pdt --theme theme-dev-test --plugin plugin-dev-test --activate theme-dev-test
bin/e2e --instances pdt --project plugin-dev-test --results pdt -- specs/block.spec.js
../plugin-dev-test/tests/cli/run pdt
bin/down pdt
```

| Spec | Covers | Code under test |
|---|---|---|
| `assets.spec.js` | Static assets of the plugin through the injected `Assets` service: URLs below the plugin, the override of an asset by the test theme (`overrides/plugin-dev-test/assets/`), `@plugin/…` without override, an inline SVG | `assets/images/`, `src/Asset/` |
| `block.spec.js` | Block of the plugin: inline editing, re-render through the plugin's render route, `registerPanel` of the plugin's instance, stored content, front end | `src/Block/Note/` |
| `coexistence.spec.js` | Two framework instances: separate runtimes, JS globals and render routes in one editor, blocks of theme and plugin in one post, hooks of both | `src/Block/Note/`, `src/Hook/` |
| `document-views.spec.js` | Document view of the plugin next to the test theme's: view data with the same key and a shared module with the same name (`#shared/order`) stay separate per instance | `assets/document.view.js`, `assets/shared/`, `src/Script/` |
| `excerpt.spec.js` | Excerpt fields of theme and plugin blocks in document order | `src/Block/Note/` |
| `twig.spec.js` | Own templates despite theme templates with the same path, `@plugin`, cache directory per slug (and per release) | `src/Twig/` |
| `styles.spec.js` | Stylesheet built with Tailwind and the prefix `pdt` (only prefixed utilities and variables), loaded on the front end and in the editor canvas, not in the editor's main document | `assets/styles.css`, `src/Block/Note/` |
| `supports.spec.js` | Block support of the plugin with the same attribute name as the theme's (`surface`): only on the plugin's block, prefixed class names, control in the Styles tab, stored attribute | `src/Support/`, `src/Block/Note/`, `assets/styles.css` |
| `parity.spec.js` | The same sizes, spacing and type of every element of a block in the editor canvas as on the front end (`measureBlocks()` in `helpers.js`); the space between blocks belongs to the theme | `src/Block/Teaser/` |
| `post-types.spec.js` | The plugin's post type `plugin_dev_test_note` (exactly 20 characters) and its meta read through its own classes; its own meta key on posts next to those of the theme; queries of notes and posts by its own meta fields (`RatingFields`, `NoteDetailsFields`, generated by `wp-app-framework meta-fields`), also for posts without a stored value | `src/Post/` |
| `settings.spec.js` | Settings group of the plugin with the same name as the theme's (`general`): own option `plugin_dev_test_general`, own page under Settings (one group, no tabs), template global with the plugin's groups only, options of the theme unchanged | `src/Setting/` |
| `view.spec.js` | View script of the plugin's block: loaded with the plugin's view runtime, marker with the plugin's slug, mounted next to the theme's view scripts (if the active test theme has them), preview in the editor | `src/Block/Counter/` |
| `tests/cli/run` | WP-CLI command of the plugin `wp plugin-dev-test check ping` with an optional argument and the namespace of the plugin, next to `wp dev-test check values` of the theme (skipped if the active theme does not offer it): two instances, separate namespaces | `src/Cli/` |
| `tests/lifecycle/run` | Deactivation, activation and uninstallation of the plugin: `#[OnActivation]`, `#[OnDeactivation]`, `#[OnUninstall]`, cron events removed on deactivation, uninstall hook registered, uninstallation of the inactive plugin removes its data and only its data | `src/Lifecycle/` |
| `mode.spec.js` | Development and release mode, prefixed classes, init action `wp_app_framework/init/plugin-dev-test`, URL from `plugin_dir_url()` | `src/Mode/` |
| `tables.spec.js` | The plugin's table `wp_plugin_dev_test_log` (the same local name as the theme's `log`): a row saved and found through its own row class and a field class written by hand; the theme's table is another one (only checked if the active theme has tables) | `src/Table/` |

## Structure

- `plugin-dev-test.php` loads the autoloader (`vendor-prefixed/` in release builds, `vendor/` in source checkouts) and boots the framework with `Plugin::boot(__FILE__)`.
- `src/` contains the plugin's PHP classes (PSR-4 `Saij\PluginDevTest\`) with their Twig templates and editor scripts.
- `build/` (not versioned) is the output of the script and stylesheet build (`assets/styles.css`, Tailwind with the prefix `pdt`, because class names are global).

Caches: `wp-content/cache/plugin-dev-test/manifest/` (development), `wp-content/cache/plugin-dev-test/twig/<template version>/[<theme versions>/]` (release).

## License

[GPL-2.0-or-later](LICENSE), like WordPress.
