<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Counter;

use Saij\WpAppFramework\Value\PlainText;

final readonly class CounterAttributes
{
    public function __construct(
        #[PlainText]
        public string $label = '',
        public int $start = 0,
    ) {}
}
