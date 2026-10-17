<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Post;

use WebCodingCat\PluginDevTest\Rest\Api;
use WebCodingCat\WpAppFramework\Post\AbstractPost;
use WebCodingCat\WpAppFramework\Post\Post;
use WebCodingCat\WpAppFramework\Query\Direction;
use WebCodingCat\WpAppFramework\Query\PostOrder;
use WebCodingCat\WpAppFramework\Rest\HttpMethod;
use WebCodingCat\WpAppFramework\Rest\Permission\Capability;
use WebCodingCat\WpAppFramework\Rest\RestController;
use WebCodingCat\WpAppFramework\Rest\RestRoute;

/**
 * Reads the plugin's meta through its own classes and queries posts by it.
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

    /**
     * Queries of the given posts (`ids`, comma-separated) by the plugin's meta: notes with a priority of at least 2,
     * by priority; posts of WordPress with more than 3 stars.
     *
     * @return array{notes: list<int|null>, rated: list<int|null>}
     */
    #[RestRoute('/query', HttpMethod::Get, new Capability('edit_posts'))]
    public function query(string $ids): array
    {
        $ids = array_map(intval(...), array_filter(explode(',', $ids), is_numeric(...)));
        $id = static fn(AbstractPost $post): ?int => $post->getId();

        return [
            'notes' => array_map($id, Note::query()->ids(...$ids)->where(NoteDetailsFields::priority()->greaterOrEqual(2))->orderBy(NoteDetailsFields::priority(), Direction::Desc)->get()),
            'rated' => array_map($id, Post::query()->ids(...$ids)->where(RatingFields::stars()->greaterThan(3))->orderBy(PostOrder::Id)->get()),
        ];
    }
}
