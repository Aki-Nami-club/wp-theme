<?php
/**
 * VibeMag theme bootstrap file.
 *
 * @package VibeMag
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once get_theme_file_path('inc/setup.php');

add_action('wp_enqueue_scripts', static function (): void {
    $theme_version = wp_get_theme()->get('Version');

    // Development-only CDN styles/scripts (replace with compiled assets in production).
    wp_enqueue_style(
        'vibemag-tailwind',
        'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
        [],
        '2.2.19'
    );

    wp_enqueue_style(
        'vibemag-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        [],
        '11.2.6'
    );

    wp_enqueue_script(
        'vibemag-swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        [],
        '11.2.6',
        true
    );

    wp_enqueue_script(
        'vibemag-main',
        get_theme_file_uri('assets/js/main.js'),
        ['vibemag-swiper'],
        $theme_version,
        true
    );
});
