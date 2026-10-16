# CLAUDE.md – plugin-dev-test

Test plugin of [wp-app-framework](https://github.com/saijsaij/wp-app-framework) (slug `plugin-dev-test`, main file `plugin-dev-test.php`, namespace `plugin-dev-test`: blocks `plugin-dev-test/…`, REST `plugin-dev-test/v1`, template prefix `plugin_dev_test_`, PHP `Saij\PluginDevTest\`). Not a product plugin: it exercises the framework in a plugin next to the test theme [theme-dev-test](https://github.com/saijsaij/theme-dev-test). Framework principles, conventions and testing rules: the [developer documentation](https://saijsaij.github.io/wp-app-framework/next/development/) (in the framework checkout `docs/content/development/`) and the framework's CLAUDE.md; this file covers the plugin.

<!-- Maintainers: keep this file under 200 lines; the README holds commands in full and the spec table. -->

## Rules

- This suite tests what the plugin brings itself and what works with every release of the test theme. What a consumer does with the plugin (public API, public template callables, overrides of plugin templates) is tested in the consumer's suite (`plugin.spec.js` of theme-dev-test, specs of [theme-dev-test-child](https://github.com/saijsaij/theme-dev-test-child)).
- The public interface (`Api\NoteApi`, `plugin_dev_test_*` template callables, `Twig/Overridable.twig`) is used by other repositories: change it only together with them, and keep its signatures free of prefixed types (PHP, WordPress and own classes only).
- The browser tests need theme-dev-test as active theme or as parent of the active child theme (`requireTestTheme()`).
- Each test deletes exactly what it created; the console guard in `tests/e2e/fixtures.js` is the same as in the test theme.
- Class properties are never `public` (exceptions: block attribute and value classes); everything in this repository is English; other repositories are linked by GitHub URL.

## Commands

Run from this directory, with [wp-test-env](https://github.com/saijsaij/wp-test-env) checked out next to it. Every check must pass without errors or warnings.

```sh
../wp-test-env/bin/tools -w plugin-dev-test composer build-manifest   # output must match the README
../wp-test-env/bin/tools -w plugin-dev-test composer lint             # fix: composer fix
../wp-test-env/bin/tools -w plugin-dev-test npm run lint              # fix: npm run format
```

Browser, WP-CLI (`tests/cli/run`, needs the test theme) and lifecycle tests (`tests/lifecycle/run`, uninstalls the plugin, own instance) run in fresh instances of wp-test-env, from its directory:

```sh
bin/run ../plugin-dev-test/tests/dev.pools                 # before a push, with the pool files of the other test projects
bin/run ../theme-dev-test/tests/release.pools ../plugin-dev-test/tests/release.pools   # to reproduce a failed release workflow
```

Every Playwright worker has an instance of its own (`WP_BASE_URLS`, `tests/e2e/playwright.config.js`); a spec must not depend on another spec file. The test timeout is 30 s.

Release build and release test: see "Release" and "Tests" in the README.

## Keep in sync

- Expected `build-manifest` output in the README when counts change.
- The framework is required as `dev-main`; after a framework change is pushed, `composer update saij/wp-app-framework`.
- A release tag `v<version>` must equal `Version:` in `plugin-dev-test.php`. `with-theme` in `.github/workflows/release.yml` names the theme release the plugin is tested with, in the release workflow and in `tests/release.pools` (`tests/download-with-theme`); it must contain everything the suite expects from the theme.
- Every GitHub release gets short release notes instead of the generated compare link alone: what the plugin tests anew, the framework version it is built with and the theme release it is tested with (`with-theme`), and the compare link last. The release workflow creates the release with `--generate-notes`; once it has passed, replace the text with `gh release edit <tag> --notes-file <file>` (a rerun only uploads the ZIP and keeps the text).
