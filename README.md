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

The framework is required from its GitHub repository as `dev-main`, like in the test theme. Clone the repositories side by side and use [wp-dev-env](https://github.com/saijsaij/wp-dev-env) with `WP_THEMES=theme-dev-test`, `WP_ACTIVE_THEME=theme-dev-test`, `WP_PLUGINS=plugin-dev-test` and `WP_PACKAGE_LINKS=saij/wp-app-framework=wp-app-framework`; its setup runs `composer install`, `npm ci` and `npm run build` for theme and plugin and activates the plugin.

```sh
git clone https://github.com/saijsaij/wp-app-framework.git
git clone https://github.com/saijsaij/theme-dev-test.git
git clone https://github.com/saijsaij/plugin-dev-test.git
```

## Release

`composer release` builds `dist/plugin-dev-test-<version>.zip` with the release pipeline of the framework (see [Release](https://saijsaij.github.io/wp-app-framework/next/build-and-release/release.html) in its documentation); the framework comes from the lock file, or from the local checkout with `-- --package-source=saij/wp-app-framework=../wp-app-framework`. A tag `v<version>` (= `Version` in `plugin-dev-test.php`) runs `.github/workflows/release.yml`: build, release test next to the release of the test theme named there (`with-theme`) with all browser tests, then the ZIP is attached to the GitHub release of the tag.

## Tests

All checks run in wp-dev-env and must pass without errors or warnings.

```sh
docker compose run --rm tools composer --working-dir=/workspace/plugin-dev-test build-manifest
docker compose run --rm tools composer --working-dir=/workspace/plugin-dev-test lint   # fix: composer fix
docker compose run --rm tools npm --prefix /workspace/plugin-dev-test run lint          # fix: npm run format
docker compose run --rm e2e npm --prefix /workspace/plugin-dev-test run test:e2e
```

`build-manifest` must report

```
Manifest OK (plugin plugin-dev-test): 27 service candidates, 18 services, 1 hooks, 11 routes, 34 template functions, 1 template filters, 3 blocks, 1 CLI commands. Nothing written (no --output).
Public: 1 APIs, 3 template functions and filters.
```

The browser tests need the test theme as active theme or as parent of the active child theme [theme-dev-test-child](https://github.com/saijsaij/theme-dev-test-child) (`requireTestTheme()` fails otherwise). Against the release ZIPs:

```sh
scripts/release-test --theme ../theme-dev-test/dist/theme-dev-test-<version>.zip --plugin ../plugin-dev-test/dist/plugin-dev-test-0.1.0.zip
set -a; . ./.env; set +a
docker compose run --rm --no-deps -e WP_BASE_URL="$WP_RELEASE_URL" -e E2E_BROWSER_HOST_RULES="MAP $WP_RELEASE_HOST traefik" \
    -e E2E_TARGET=release -e E2E_OUTPUT_DIR=/results/release e2e npm --prefix /workspace/plugin-dev-test run test:e2e
```

The console guard in `tests/e2e/fixtures.js` is the same as in the test theme.

WP-CLI tests (`tests/cli/run`, Bash) run the plugin's command next to those of the test theme; like the browser tests they need the test theme with its WP-CLI commands. Call the script from the wp-dev-env directory with the Compose service of the site (`wordpress-release` after `scripts/release-test`; the release workflow does so with `cli-tests: true`):

```sh
../plugin-dev-test/tests/cli/run wordpress
../plugin-dev-test/tests/cli/run wordpress-release
```

| Spec | Covers | Code under test |
|---|---|---|
| `block.spec.js` | Block of the plugin: inline editing, re-render through the plugin's render route, `registerPanel` of the plugin's instance, stored content, front end | `src/Block/Note/` |
| `coexistence.spec.js` | Two framework instances: separate runtimes, JS globals and render routes in one editor, blocks of theme and plugin in one post, hooks of both | `src/Block/Note/`, `src/Hook/` |
| `excerpt.spec.js` | Excerpt fields of theme and plugin blocks in document order | `src/Block/Note/` |
| `twig.spec.js` | Own templates despite theme templates with the same path, `@plugin`, cache directory per slug (and per release) | `src/Twig/` |
| `styles.spec.js` | Stylesheet built with Tailwind and the prefix `pdt` (only prefixed utilities and variables), loaded on the front end and in the editor canvas, not in the editor's main document | `src/styles.css`, `src/Block/Note/` |
| `supports.spec.js` | Block support of the plugin with the same attribute name as the theme's (`surface`): only on the plugin's block, prefixed class names, control in the Styles tab, stored attribute | `src/Support/`, `src/Block/Note/`, `src/styles.css` |
| `parity.spec.js` | The same sizes, spacing and type of every element of a block in the editor canvas as on the front end (`measureBlocks()` in `helpers.js`); the space between blocks belongs to the theme | `src/Block/Teaser/` |
| `post-types.spec.js` | The plugin's post type `plugin_dev_test_note` (exactly 20 characters) and its meta read through its own classes; its own meta key on posts next to those of the theme; queries of notes and posts by its own meta fields (`RatingFields`, `NoteDetailsFields`, generated by `wp-app-framework meta-fields`), also for posts without a stored value | `src/Post/` |
| `settings.spec.js` | Settings group of the plugin with the same name as the theme's (`general`): own option `plugin_dev_test_general`, own page under Settings (one group, no tabs), template global with the plugin's groups only, options of the theme unchanged | `src/Setting/` |
| `view.spec.js` | View script of the plugin's block: loaded with the plugin's view runtime, marker with the plugin's slug, mounted next to the theme's view scripts (if the active test theme has them), preview in the editor | `src/Block/Counter/` |
| `tests/cli/run` | WP-CLI command of the plugin `wp plugin-dev-test check ping` with an optional argument and the namespace of the plugin, next to `wp dev-test check values` of the theme: two instances, separate namespaces | `src/Cli/` |
| `mode.spec.js` | Development and release mode, prefixed classes, init action `wp_app_framework/init/plugin-dev-test`, URL from `plugin_dir_url()` | `src/Mode/` |

## Structure

- `plugin-dev-test.php` loads the autoloader (`vendor-prefixed/` in release builds, `vendor/` in source checkouts) and boots the framework with `Plugin::boot(__FILE__)`.
- `src/` contains the plugin's PHP classes (PSR-4 `Saij\PluginDevTest\`) with their Twig templates and editor scripts.
- `build/` (not versioned) is the output of the script and stylesheet build (`src/styles.css`, Tailwind with the prefix `pdt`, because class names are global).

Caches: `wp-content/cache/plugin-dev-test/manifest/` (development), `wp-content/cache/plugin-dev-test/twig/<template version>/[<theme versions>/]` (release).
