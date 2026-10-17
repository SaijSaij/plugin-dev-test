<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Asset;

use WebCodingCat\PluginDevTest\Rest\Api;
use WebCodingCat\WpAppFramework\Asset\Assets;
use WebCodingCat\WpAppFramework\Rest\HttpMethod;
use WebCodingCat\WpAppFramework\Rest\Permission\PublicAccess;
use WebCodingCat\WpAppFramework\Rest\RestController;
use WebCodingCat\WpAppFramework\Rest\RestRoute;

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
