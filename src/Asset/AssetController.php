<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Asset;

use Saij\PluginDevTest\Rest\Api;
use Saij\WpAppFramework\Asset\Assets;
use Saij\WpAppFramework\Rest\HttpMethod;
use Saij\WpAppFramework\Rest\Permission\PublicAccess;
use Saij\WpAppFramework\Rest\RestController;
use Saij\WpAppFramework\Rest\RestRoute;

/**
 * Static assets of the plugin through the injected service: the theme overrides images/badge.svg in
 * overrides/plugin-dev-test/assets/ (assets.spec.js).
 */
#[RestController(Api::VERSION, base: '/assets')]
final readonly class AssetController
{
    public function __construct(private Assets $assets) {}

    /**
     * @return array{badge: string, exact: string, plain: string, svg: string}
     */
    #[RestRoute('', HttpMethod::Get, new PublicAccess())]
    public function urls(): array
    {
        return [
            'badge' => $this->assets->url('images/badge.svg'),
            'exact' => $this->assets->url('@plugin/images/badge.svg'),
            'plain' => $this->assets->url('images/plain.svg'),
            'svg' => $this->assets->svg('images/plain.svg', ['class' => 'pdt-asset']),
        ];
    }
}
