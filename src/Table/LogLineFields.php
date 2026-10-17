<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Table;

use WebCodingCat\WpAppFramework\Query\Field\StringField;
use WebCodingCat\WpAppFramework\Table\TableFields;

/**
 * Written by hand, with the one field the plugin queries.
 */
#[TableFields(LogLine::class)]
final class LogLineFields
{
    public static function message(): StringField
    {
        return new StringField(LogLine::class, 'message');
    }
}
