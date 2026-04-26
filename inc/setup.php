<?php
/**
 * Theme setup.
 *
 * @package VibeMag
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-line-height');
    add_theme_support('custom-spacing');

    load_theme_textdomain('vibemag', get_template_directory() . '/languages');
});
