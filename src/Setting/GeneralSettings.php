<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Setting;

use Saij\WpAppFramework\Setting\SettingsGroup;

/**
 * The plugin's settings group, with the same name as a group of the test theme: option plugin_dev_test_general.
 */
#[SettingsGroup('general', title: 'General')]
final readonly class GeneralSettings
{
    public function __construct(
        public string $greeting = 'Hello from the plugin',
        public bool $enabled = true,
    ) {}
}
