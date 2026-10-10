<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Note;

use Saij\WpAppFramework\Block\Block;
use Saij\WpAppFramework\Block\BlockCategory;
use Saij\WpAppFramework\Twig\TemplateRenderer;

/**
 * Block of the plugin, used next to the blocks of the test theme.
 */
#[Block('plugin-dev-test/note', title: 'Note', category: BlockCategory::Text, icon: 'sticky', description: 'A note from the test plugin.')]
final readonly class Note
{
    public function __construct(private TemplateRenderer $renderer) {}

    public function render(NoteAttributes $attrs): string
    {
        return $this->renderer->render('Block/Note/Note.twig', ['attrs' => $attrs]);
    }
}
