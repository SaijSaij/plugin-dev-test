<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Teaser;

use WebCodingCat\WpAppFramework\Value\Label;
use WebCodingCat\WpAppFramework\Value\PlainText;
use WebCodingCat\WpAppFramework\Value\RichText;
use WebCodingCat\WpAppFramework\Value\Url;

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
