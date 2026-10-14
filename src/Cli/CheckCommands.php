<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Cli;

use Saij\WpAppFramework\WpCli\Argument;
use Saij\WpAppFramework\WpCli\CliCommand;
use Saij\WpAppFramework\WpCli\CliOutput;
use Saij\WpAppFramework\WpCli\CliSubcommand;

/**
 * WP-CLI commands `wp plugin-dev-test check …`, run by tests/cli/run next to those of the test theme.
 */
#[CliCommand('check', 'Commands that check the WP-CLI support of the plugin.')]
final readonly class CheckCommands
{
    #[CliSubcommand('ping', 'Answer with pong and the name of the plugin.')]
    public function ping(CliOutput $output, #[Argument('Text appended to the answer.')] string $suffix = ''): void
    {
        $output->log(trim('pong from plugin-dev-test ' . $suffix));
    }
}
