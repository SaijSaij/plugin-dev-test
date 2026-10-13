<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Post;

use Saij\PluginDevTest\Rest\Api;
use Saij\WpAppFramework\Post\Post;
use Saij\WpAppFramework\Rest\HttpMethod;
use Saij\WpAppFramework\Rest\Permission\Capability;
use Saij\WpAppFramework\Rest\RestController;
use Saij\WpAppFramework\Rest\RestRoute;

/**
 * Reads the plugin's meta through its own classes.
 */
#[RestController(Api::VERSION, base: '/posts')]
final readonly class PostController
{
    /**
     * @return array{class: class-string, priority: int}
     */
    #[RestRoute('/notes/{id}', HttpMethod::Get, new Capability('edit_posts'))]
    public function note(int $id): array
    {
        $note = Note::get($id);

        return ['class' => $note::class, 'priority' => $note->meta(NoteDetails::class)->getPriority()];
    }

    /**
     * @return array{stars: int}
     */
    #[RestRoute('/rating/{id}', HttpMethod::Get, new Capability('edit_posts'))]
    public function rating(int $id): array
    {
        return ['stars' => Post::get($id)->meta(Rating::class)->getStars()];
    }
}
