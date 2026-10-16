<?php

/**
 * Plugin Name: Dev Test Plugin
 * Description: Test plugin for wp-app-framework: its build and browser tests run against this plugin next to the test theme.
 * Version: 0.15.1
 * Requires at least: 7.1
 * Requires PHP: 8.5
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: plugin-dev-test
 */

declare(strict_types=1);

// Release builds ship prefixed dependencies in vendor-prefixed/; source checkouts use Composer's vendor/.
require is_file(__DIR__ . '/vendor-prefixed/autoload.php')
    ? __DIR__ . '/vendor-prefixed/autoload.php'
    : __DIR__ . '/vendor/autoload.php';

Saij\WpAppFramework\Plugin::boot(__FILE__);
