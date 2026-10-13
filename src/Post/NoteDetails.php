<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Post;

use Saij\WpAppFramework\Post\PostMeta;

#[PostMeta('details', title: 'Note details', postTypes: [Note::class])]
final class NoteDetails
{
    public function __construct(
        private int $priority = 1,
    ) {}

    public function getPriority(): int
    {
        return $this->priority;
    }
}
