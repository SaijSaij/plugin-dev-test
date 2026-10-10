<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Note;

use Saij\WpAppFramework\Block\PlainText;
use Saij\WpAppFramework\Block\RichText;

final readonly class NoteAttributes
{
    public function __construct(
        #[PlainText(excerpt: true)]
        public string $title = '',
        #[RichText(allowedFormats: ['core/bold'])]
        public string $text = '',
        public Tone $tone = Tone::Info,
    ) {}
}
