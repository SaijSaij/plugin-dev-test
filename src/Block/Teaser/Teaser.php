<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Teaser;

use Saij\WpAppFramework\Block\Block;
use Saij\WpAppFramework\Block\BlockCategory;
use Saij\WpAppFramework\Twig\TemplateRenderer;

/**
 * Styled with the prefixed utilities of the plugin's stylesheet; must look the
 * same in the editor canvas and on the front end (parity.spec.js).
 */
#[Block('teaser', title: 'Teaser', category: BlockCategory::Design, icon: 'id-alt', description: 'Teaser with title, text and link.')]
final readonly class Teaser
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function render(TeaserAttributes $attrs): string
    {
        return $this->renderer->render('Block/Teaser/Teaser.twig', ['attrs' => $attrs]);
    }
}
