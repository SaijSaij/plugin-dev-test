<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Mode;

use Saij\PluginDevTest\Rest\Api;
use Saij\WpAppFramework\Plugin;
use Saij\WpAppFramework\Rest\HttpMethod;
use Saij\WpAppFramework\Rest\Permission\PublicAccess;
use Saij\WpAppFramework\Rest\RestController;
use Saij\WpAppFramework\Rest\RestRoute;
use Twig\Environment;

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
