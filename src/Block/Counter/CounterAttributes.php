<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Counter;

use WebCodingCat\WpAppFramework\Value\PlainText;

final readonly class CounterAttributes
{
    public function __construct(
        #[PlainText]
        public string $label = '',
        public int $start = 0,
    ) {}
}
