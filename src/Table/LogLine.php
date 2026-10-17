<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Table;

use WebCodingCat\WpAppFramework\Table\AbstractRow;
use WebCodingCat\WpAppFramework\Table\Indexed;
use WebCodingCat\WpAppFramework\Table\PrimaryKey;
use WebCodingCat\WpAppFramework\Table\Table;

/**
 * A row of the table wp_plugin_dev_test_log: the same local name as the log table of the test theme, a table of
 * its own because the plugin has a namespace of its own.
 */
#[Table('log')]
final class LogLine extends AbstractRow
{
    public function __construct(
        #[PrimaryKey]
        private ?int $id = null,
        #[Indexed]
        private string $message = '',
        private int $count = 0,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
