<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Twig;

use WebCodingCat\PluginDevTest\Api\NoteApi;
use WebCodingCat\WpAppFramework\Twig\TemplateFilter;
use WebCodingCat\WpAppFramework\Twig\TemplateFunction;
use WebCodingCat\WpAppFramework\Twig\TemplateRenderer;

/**
 * Public template functions and filters of the plugin; the test theme calls them by name.
 */
final readonly class NoteTemplates
{
    public function __construct(
        private NoteApi $notes,
        private TemplateRenderer $renderer,
    ) {}

    #[TemplateFunction('plugin_dev_test_note_count', public: true)]
    public function noteCount(): int
    {
        return count($this->notes->getNotes());
    }

    /**
     * Renders Twig/Partial.twig of the plugin; the theme has a template with the same name.
     */
    #[TemplateFunction('plugin_dev_test_partial', html: true, public: true)]
    public function partial(string $name): string
    {
        return $this->renderer->render('Twig/Partial.twig', ['name' => $name]);
    }

    #[TemplateFilter('plugin_dev_test_highlight', html: true, public: true)]
    public function highlight(string $text): string
    {
        return '<mark class="plugin-dev-test">' . $text . '</mark>';
    }
}
