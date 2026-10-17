<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Lifecycle;

use WebCodingCat\WpAppFramework\Lifecycle\OnActivation;
use WebCodingCat\WpAppFramework\Lifecycle\OnDeactivation;
use WebCodingCat\WpAppFramework\Lifecycle\OnUninstall;

/**
 * Counts activations, deactivations and uninstallations in the option plugin_dev_test_lifecycle_probe, which
 * tests/lifecycle/run reads. The option is no data the framework knows of, so the uninstallation leaves it; the test
 * deletes it.
 */
final readonly class LifecycleProbe
{
    public const string OPTION = 'plugin_dev_test_lifecycle_probe';

    #[OnActivation]
    public function activated(): void
    {
        $this->count('activation');
    }

    #[OnDeactivation]
    public function deactivated(): void
    {
        $this->count('deactivation');
    }

    #[OnUninstall]
    public function uninstalled(): void
    {
        $this->count('uninstall');
    }

    private function count(string $phase): void
    {
        $counts = get_option(self::OPTION, []);
        $counts = is_array($counts) ? $counts : [];
        $previous = $counts[$phase] ?? 0;
        $counts[$phase] = (is_int($previous) ? $previous : 0) + 1;
        update_option(self::OPTION, $counts, false);
    }
}
