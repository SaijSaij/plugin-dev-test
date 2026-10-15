<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Teaser;

use Saij\WpAppFramework\Value\Label;
use Saij\WpAppFramework\Value\PlainText;
use Saij\WpAppFramework\Value\RichText;
use Saij\WpAppFramework\Value\Url;

final readonly class TeaserAttributes
{
    public function __construct(
        #[RichText(allowedFormats: ['core/bold', 'core/italic'])]
        public string $title = '',
        #[RichText(allowedFormats: ['core/bold', 'core/italic'])]
        public string $text = '',
        #[PlainText]
        public string $linkLabel = '',
        #[Url]
        #[Label('Link URL')]
        public string $linkUrl = '',
    ) {}
}
