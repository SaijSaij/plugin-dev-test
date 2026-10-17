<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Block\Note;

use WebCodingCat\WpAppFramework\Value\Labeled;

enum Tone: string implements Labeled
{
    case Info = 'info';
    case Warning = 'warning';

    public function label(): string
    {
        return match ($this) {
            self::Info => 'Info',
            self::Warning => 'Warning',
        };
    }
}
