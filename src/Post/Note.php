<?php

declare(strict_types=1);

namespace WebCodingCat\PluginDevTest\Post;

use WebCodingCat\WpAppFramework\Post\AbstractPost;
use WebCodingCat\WpAppFramework\Post\PostType;
use WebCodingCat\WpAppFramework\Post\PostTypeSupport;

/**
 * A post type of the plugin: plugin_dev_test_note, exactly the 20 characters WordPress allows.
 */
#[PostType('note', label: 'Notes', singularLabel: 'Note', public: false, supports: [PostTypeSupport::Title, PostTypeSupport::Editor])]
final class Note extends AbstractPost {}
