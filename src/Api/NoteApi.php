<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Api;

use Saij\WpAppFramework\Api\PublicApi;

/**
 * Public API of the plugin; the test theme injects it with #[PluginApi('plugin-dev-test')].
 * Its signatures use only PHP and own classes (checked by the build).
 */
#[PublicApi]
final class NoteApi
{
    /**
     * @return list<NoteSummary>
     */
    public function getNotes(): array
    {
        return [new NoteSummary('First note', 'info'), new NoteSummary('Second note', 'warning')];
    }
}
