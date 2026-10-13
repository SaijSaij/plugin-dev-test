<?php

declare(strict_types=1);

namespace Saij\PluginDevTest\Post;

use Saij\WpAppFramework\Post\AbstractPost;
use Saij\WpAppFramework\Post\PostType;
use Saij\WpAppFramework\Post\PostTypeSupport;

/**
 * A post type of the plugin: plugin_dev_test_note, exactly the 20 characters WordPress allows.
 */
#[PostType('note', label: 'Notes', singularLabel: 'Note', public: false, supports: [PostTypeSupport::Title, PostTypeSupport::Editor])]
final class Note extends AbstractPost {}
