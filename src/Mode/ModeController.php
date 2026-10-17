<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Mode;

use Twig\Environment;
use WebCodingCat\PluginDevTest\Rest\Api;
use WebCodingCat\WpAppFramework\Plugin;
use WebCodingCat\WpAppFramework\Rest\HttpMethod;
use WebCodingCat\WpAppFramework\Rest\Permission\PublicAccess;
use WebCodingCat\WpAppFramework\Rest\RestController;
use WebCodingCat\WpAppFramework\Rest\RestRoute;

/**
 * Reports the mode and the paths of the plugin's framework instance.
 */
#[RestController(Api::VERSION)]
final readonly class ModeController
{
    public function __construct(private Plugin $plugin) {}

    /**
     * Class names as the plugin code sees them: prefixed in a release (Strauss rewrites the call sites).
     *
     * @return array{release: bool, devCache: bool, frameworkClass: string, twigClass: string, init: int, directoryUri: string}
     */
    #[RestRoute('/mode', HttpMethod::Get, new PublicAccess())]
    public function mode(): array
    {
        return [
            'release' => is_file($this->plugin->getDirectory() . '/' . Plugin::RELEASE_MANIFEST),
            'devCache' => is_dir(WP_CONTENT_DIR . '/cache/' . $this->plugin->getSlug() . '/manifest'),
            'frameworkClass' => Plugin::class,
            'twigClass' => Environment::class,
            'init' => did_action('wp_app_framework/init/' . $this->plugin->getSlug()),
            'directoryUri' => $this->plugin->getDirectoryUri(),
        ];
    }
}
