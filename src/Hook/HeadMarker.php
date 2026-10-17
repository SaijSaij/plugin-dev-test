<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Hook;

use WebCodingCat\WpAppFramework\Hook\Hook;
use WebCodingCat\WpAppFramework\Plugin;

/**
 * Action of the plugin: prints a meta tag the browser tests look for.
 */
final readonly class HeadMarker
{
    public function __construct(private Plugin $plugin) {}

    #[Hook('wp_head')]
    public function print(): void
    {
        printf('<meta name="plugin-dev-test-hook" content="%s">' . "\n", esc_attr('Hello from ' . $this->plugin->getSlug()));
    }
}
