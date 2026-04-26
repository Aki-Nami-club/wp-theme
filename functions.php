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

    wp_enqueue_style(
        'vibemag-main',
        get_theme_file_uri('assets/css/main.css'),
        ['vibemag-tailwind', 'vibemag-swiper'],
        $theme_version
    );

    wp_enqueue_script(
        'vibemag-alpine',
        'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js',
        [],
        '3.14.8',
        true
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
        ['vibemag-alpine', 'vibemag-swiper'],
        $theme_version,
        true
    );

    wp_localize_script(
        'vibemag-main',
        'vibemagSettings',
        [
            'restUrl' => esc_url_raw(rest_url('wp/v2/search')),
            'homeUrl' => esc_url_raw(home_url('/')),
            'searchNonce' => wp_create_nonce('wp_rest'),
        ]
    );
});

add_shortcode('vibemag_news_ticker', static function (): string {
    $query = new WP_Query(
        [
            'post_type'           => 'post',
            'posts_per_page'      => 8,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]
    );

    if (! $query->have_posts()) {
        return '<p class="vibemag-ticker-empty">' . esc_html__('No news yet.', 'vibemag') . '</p>';
    }

    $output = '<div class="vibemag-ticker-track" aria-label="' . esc_attr__('Latest news ticker', 'vibemag') . '">';

    while ($query->have_posts()) {
        $query->the_post();
        $output .= sprintf(
            '<a class="vibemag-ticker-link" href="%1$s">%2$s</a>',
            esc_url(get_permalink()),
            esc_html(get_the_title())
        );
    }

    $output .= '</div>';

    wp_reset_postdata();

    return $output;
});
