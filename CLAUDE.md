# CLAUDE.md – plugin-dev-test

Test plugin of [wp-app-framework](https://github.com/saijsaij/wp-app-framework) (slug `plugin-dev-test`, main file `plugin-dev-test.php`, blocks `plugin-dev-test/…`, REST `plugin-dev-test/v1`, template prefix `plugin_dev_test_`, PHP `Saij\PluginDevTest\`). Not a product plugin: it exercises the framework in a plugin next to the test theme [theme-dev-test](https://github.com/saijsaij/theme-dev-test). Framework principles and rules: the framework's CLAUDE.md and its [documentation](https://saijsaij.github.io/wp-app-framework/); this file covers the plugin.

<!-- Maintainers: keep this file under 200 lines; the README holds commands in full and the spec table. -->

## Rules

- This suite tests what the plugin brings itself and what works with every release of the test theme. What a consumer does with the plugin (public API, public template callables, overrides of plugin templates) is tested in the consumer's suite (`plugin.spec.js` of theme-dev-test, specs of [theme-dev-test-child](https://github.com/saijsaij/theme-dev-test-child)).
- The public interface (`Api\NoteApi`, `plugin_dev_test_*` template callables, `Twig/Overridable.twig`) is used by other repositories: change it only together with them, and keep its signatures free of prefixed types (PHP, WordPress and own classes only).
- The browser tests need theme-dev-test as active theme or as parent of the active child theme (`requireTestTheme()`).
- Each test deletes exactly what it created; the console guard in `tests/e2e/fixtures.js` is the same as in the test theme.
- Class properties are never `public` (exceptions: block attribute and value classes); everything in this repository is English; other repositories are linked by GitHub URL.

## Commands

Run from a [wp-dev-env](https://github.com/saijsaij/wp-dev-env) checkout next to this repository. Every check must pass without errors or warnings.

```sh
docker compose run --rm tools composer --working-dir=/workspace/plugin-dev-test build-manifest   # output must match the README
docker compose run --rm tools composer --working-dir=/workspace/plugin-dev-test lint             # fix: composer fix
docker compose run --rm tools npm --prefix /workspace/plugin-dev-test run lint                    # fix: npm run format
docker compose run --rm e2e npm --prefix /workspace/plugin-dev-test run test:e2e
```

Release build and release test: see "Release" and "Tests" in the README.

## Keep in sync

- Expected `build-manifest` output in the README when counts change.
- The framework is required as `dev-main`; after a framework change is pushed, `composer update saij/wp-app-framework`.
- A release tag `v<version>` must equal `Version:` in `plugin-dev-test.php`. `with-theme` in `.github/workflows/release.yml` names the theme release the plugin is tested with; it must contain everything the suite expects from the theme.
