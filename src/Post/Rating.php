<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Post;

use WebCodingCat\WpAppFramework\Post\Post;
use WebCodingCat\WpAppFramework\Post\PostMeta;

/**
 * Meta of the plugin on posts of WordPress, next to the meta of the theme.
 */
#[PostMeta('rating', title: 'Rating', postTypes: [Post::class])]
final class Rating
{
    public function __construct(
        private int $stars = 0,
    ) {}

    public function getStars(): int
    {
        return $this->stars;
    }
}
