<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Block\Note;

use Saij\WpAppFramework\Block\Labeled;

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
