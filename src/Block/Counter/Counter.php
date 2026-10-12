<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Counter;

use Saij\WpAppFramework\Block\Block;
use Saij\WpAppFramework\Block\BlockCategory;
use Saij\WpAppFramework\Twig\TemplateRenderer;

/**
 * A button that counts its clicks: front-end behavior from the view script
 * Counter.view.js, mounted by the plugin's own view runtime next to the
 * theme's (view.spec.js).
 */
#[Block('plugin-dev-test/counter', title: 'Counter', category: BlockCategory::Widgets, icon: 'plus', description: 'A button that counts its clicks.')]
final readonly class Counter
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function render(CounterAttributes $attrs): string
    {
        return $this->renderer->render('Block/Counter/Counter.twig', ['attrs' => $attrs]);
    }
}
