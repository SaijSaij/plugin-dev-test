<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Counter;

use WebCodingCat\WpAppFramework\Block\Block;
use WebCodingCat\WpAppFramework\Block\BlockCategory;
use WebCodingCat\WpAppFramework\Twig\TemplateRenderer;

/**
 * A button that counts its clicks: front-end behavior from the view script
 * Counter.view.js, mounted by the plugin's own view runtime next to the
 * theme's (view.spec.js).
 */
#[Block('counter', title: 'Counter', category: BlockCategory::Widgets, icon: 'plus', description: 'A button that counts its clicks.')]
final readonly class Counter
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function render(CounterAttributes $attrs): string
    {
        return $this->renderer->render('Block/Counter/Counter.twig', ['attrs' => $attrs]);
    }
}
