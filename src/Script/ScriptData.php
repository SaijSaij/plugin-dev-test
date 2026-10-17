<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Script;

use WebCodingCat\WpAppFramework\Hook\Hook;
use WebCodingCat\WpAppFramework\View\ViewData;

/**
 * View data for the plugin's document view, with the same key as the test theme's: each instance has its own
 * (document-views.spec.js).
 */
final readonly class ScriptData
{
    #[Hook('wp')]
    public function document(ViewData $viewData): void
    {
        $viewData->set('document', ['greeting' => 'Hello from the plugin']);
    }
}
