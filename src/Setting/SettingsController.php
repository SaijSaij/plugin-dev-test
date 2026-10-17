<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Setting;

use WebCodingCat\PluginDevTest\Rest\Api;
use WebCodingCat\WpAppFramework\Rest\HttpMethod;
use WebCodingCat\WpAppFramework\Rest\Permission\Capability;
use WebCodingCat\WpAppFramework\Rest\Permission\PublicAccess;
use WebCodingCat\WpAppFramework\Rest\RestController;
use WebCodingCat\WpAppFramework\Rest\RestRoute;
use WebCodingCat\WpAppFramework\Twig\TemplateRenderer;
use WP_REST_Request;

/**
 * Shows the injected settings group and the template global of the plugin's
 * instance, and reads and writes the raw option (to restore it after a test).
 */
#[RestController(Api::VERSION, base: '/settings')]
final readonly class SettingsController
{
    private const string OPTION = 'plugin_dev_test_general';

    public function __construct(
        private GeneralSettings $general,
        private TemplateRenderer $renderer,
    ) {}

    /**
     * @return array{greeting: string, enabled: bool, html: string}
     */
    #[RestRoute('', HttpMethod::Get, new PublicAccess())]
    public function values(): array
    {
        return [
            'greeting' => $this->general->greeting,
            'enabled' => $this->general->enabled,
            'html' => $this->renderer->render('Setting/Settings.twig'),
        ];
    }

    #[RestRoute('/raw', HttpMethod::Get, new Capability('manage_options'))]
    public function raw(): mixed
    {
        return get_option(self::OPTION, null);
    }

    /**
     * Stores the value of "general" as it is; null deletes the option.
     */
    #[RestRoute('/raw', HttpMethod::Post, new Capability('manage_options'))]
    public function store(WP_REST_Request $request): void
    {
        $value = $request->get_json_params()['general'] ?? null;
        if ($value === null) {
            delete_option(self::OPTION);
        } else {
            update_option(self::OPTION, $value);
        }
    }
}
