<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Note;

use WebCodingCat\PluginDevTest\Support\Surface;
use WebCodingCat\WpAppFramework\Block\Block;
use WebCodingCat\WpAppFramework\Block\BlockCategory;
use WebCodingCat\WpAppFramework\Block\Support\UseSupport;
use WebCodingCat\WpAppFramework\Twig\TemplateRenderer;

/**
 * Block of the plugin, used next to the blocks of the test theme; uses the
 * plugin's block support Surface.
 */
#[Block('note', title: 'Note', category: BlockCategory::Text, icon: 'sticky', description: 'A note from the test plugin.')]
#[UseSupport(Surface::class)]
final readonly class Note
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function render(NoteAttributes $attrs): string
    {
        return $this->renderer->render('Block/Note/Note.twig', ['attrs' => $attrs]);
    }
}
