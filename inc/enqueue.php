<?php
/**
 * Enqueue scripts and styles.
 *
 * @package VibeMag
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_style(
        'vibemag-main',
        get_theme_file_uri('assets/css/main.css'),
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'vibemag-main',
        get_theme_file_uri('assets/js/main.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );
});
