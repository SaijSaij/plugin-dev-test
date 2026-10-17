<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Support;

use WebCodingCat\WpAppFramework\Block\Support\BlockSupport;

/**
 * Block support of the plugin with the same attribute name as the test theme's
 * Surface support: each instance adds its own support to its own blocks only.
 * Class names carry the plugin's prefix, like its utilities.
 */
#[BlockSupport('surface', label: 'Shade')]
final class Surface
{
    public function className(Shade $value = Shade::Plain): string
    {
        return 'pdt-surface-' . $value->value;
    }
}
