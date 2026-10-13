<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Twig;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Saij\PluginDevTest\Rest\Api;
use Saij\WpAppFramework\Rest\HttpMethod;
use Saij\WpAppFramework\Rest\Permission\PublicAccess;
use Saij\WpAppFramework\Rest\RestController;
use Saij\WpAppFramework\Rest\RestRoute;
use Saij\WpAppFramework\Twig\TemplateRenderer;
use SplFileInfo;

/**
 * Renders the plugin's templates through its renderer.
 */
#[RestController(Api::VERSION, base: '/twig')]
final readonly class TwigController
{
    public function __construct(private TemplateRenderer $renderer) {}

    /**
     * Twig/Partial.twig of the plugin; the theme has a template with the same name.
     */
    #[RestRoute('/partial', HttpMethod::Get, new PublicAccess())]
    public function partial(string $name = 'World'): string
    {
        return $this->renderer->render('Twig/Partial.twig', ['name' => $name]);
    }

    /**
     * Twig/Overridable.twig: overridden by overrides/plugin-dev-test/ of the active theme, if it has one.
     */
    #[RestRoute('/overridable', HttpMethod::Get, new PublicAccess())]
    public function overridable(): string
    {
        return $this->renderer->render('Twig/Overridable.twig');
    }

    /**
     * The plugin's own Twig/Overridable.twig, never overridden.
     */
    #[RestRoute('/overridable-exact', HttpMethod::Get, new PublicAccess())]
    public function overridableExact(): string
    {
        return $this->renderer->render('@plugin/Twig/Overridable.twig');
    }

    /**
     * Renders a template and reports the cache directory of the instance: directories (relative) and compiled templates.
     *
     * @return array{directory: string, directories: list<string>, compiled: int}
     */
    #[RestRoute('/cache', HttpMethod::Get, new PublicAccess())]
    public function cache(): array
    {
        $this->renderer->render('Twig/Overridable.twig');
        $directory = WP_CONTENT_DIR . '/cache/plugin-dev-test/twig';
        $directories = [];
        $compiled = 0;
        if (is_dir($directory)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
            );
            /** @var SplFileInfo $entry */
            foreach ($iterator as $entry) {
                if ($entry->isDir()) {
                    $directories[] = substr($entry->getPathname(), strlen($directory) + 1);
                } elseif ($entry->getExtension() === 'php') {
                    $compiled++;
                }
            }
            sort($directories);
        }

        return ['directory' => substr($directory, strlen(ABSPATH)), 'directories' => $directories, 'compiled' => $compiled];
    }
}
