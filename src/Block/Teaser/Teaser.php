<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Teaser;

use WebCodingCat\WpAppFramework\Block\Block;
use WebCodingCat\WpAppFramework\Block\BlockCategory;
use WebCodingCat\WpAppFramework\Twig\TemplateRenderer;

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
