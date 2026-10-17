<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Post;

use WebCodingCat\WpAppFramework\Post\PostMeta;

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
