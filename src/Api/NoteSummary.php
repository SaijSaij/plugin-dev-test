<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Api;

use Saij\WpAppFramework\DependencyInjection\NoService;

/**
 * A note as the public API reports it.
 */
#[NoService]
final readonly class NoteSummary
{
    public function __construct(
        private string $title,
        private string $tone,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTone(): string
    {
        return $this->tone;
    }
}
