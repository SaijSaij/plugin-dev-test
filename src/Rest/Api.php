<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Rest;

use Saij\WpAppFramework\DependencyInjection\NoService;

#[NoService]
final class Api
{
    public const string VERSION = 'v1';
}
