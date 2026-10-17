<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Note;

use WebCodingCat\WpAppFramework\Value\PlainText;
use WebCodingCat\WpAppFramework\Value\RichText;

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
