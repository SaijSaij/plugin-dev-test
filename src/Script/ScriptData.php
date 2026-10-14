<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Script;

use Saij\WpAppFramework\Hook\Hook;
use Saij\WpAppFramework\View\ViewData;

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
