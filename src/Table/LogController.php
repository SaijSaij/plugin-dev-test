<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Table;

use WebCodingCat\PluginDevTest\Rest\Api;
use WebCodingCat\WpAppFramework\Rest\HttpMethod;
use WebCodingCat\WpAppFramework\Rest\Permission\Capability;
use WebCodingCat\WpAppFramework\Rest\RestController;
use WebCodingCat\WpAppFramework\Rest\RestException;
use WebCodingCat\WpAppFramework\Rest\RestRoute;

/**
 * The log table of the plugin over REST: rows created, found by message and deleted.
 */
#[RestController(Api::VERSION, base: '/log')]
final readonly class LogController
{
    /**
     * @return array{id: int|null, table: string}
     */
    #[RestRoute('', HttpMethod::Post, new Capability('manage_options'))]
    public function create(string $message, int $count = 0): array
    {
        $line = new LogLine(message: $message, count: $count);
        $line->save();

        return ['id' => $line->getId(), 'table' => LogLine::tableName()];
    }

    /**
     * @return array{ids: list<int|null>, counts: list<int>}
     */
    #[RestRoute('', HttpMethod::Get, new Capability('manage_options'))]
    public function find(string $message): array
    {
        $lines = LogLine::query()->where(LogLineFields::message()->equals($message))->get();

        return [
            'ids' => array_map(static fn(LogLine $line): ?int => $line->getId(), $lines),
            'counts' => array_map(static fn(LogLine $line): int => $line->getCount(), $lines),
        ];
    }

    /**
     * @return array{deleted: bool}
     */
    #[RestRoute('/{id}', HttpMethod::Delete, new Capability('manage_options'))]
    public function delete(int $id): array
    {
        $line = LogLine::find($id) ?? throw new RestException('plugin_dev_test_not_found', 'No such row.', 404);
        $line->delete();

        return ['deleted' => true];
    }
}
