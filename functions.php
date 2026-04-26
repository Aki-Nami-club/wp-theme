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

add_shortcode('vibemag_home_slider', static function (): string {
    $query = new WP_Query(
        [
            'post_type'           => 'post',
            'posts_per_page'      => 16,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]
    );

    if (! $query->have_posts()) {
        return '<p>' . esc_html__('Add at least one post to populate the homepage slider.', 'vibemag') . '</p>';
    }

    $posts = [];

    while ($query->have_posts()) {
        $query->the_post();
        $posts[] = [
            'title'     => get_the_title(),
            'permalink' => get_permalink(),
            'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
        ];
    }

    wp_reset_postdata();

    $slides = array_chunk($posts, 4);
    $slides = array_slice($slides, 0, 4);

    $output = '<div class="swiper vibemag-home-slider"><div class="swiper-wrapper">';

    foreach ($slides as $slide_posts) {
        $output .= '<div class="swiper-slide"><div class="vibemag-slide-grid">';

        foreach ($slide_posts as $item) {
            $thumb = $item['thumb'] ?: get_theme_file_uri('assets/images/placeholders/default-post-thumbnail.png');

            $output .= sprintf(
                '<article class="vibemag-slide-card"><a href="%1$s"><img src="%2$s" alt="%3$s" loading="lazy" /><h3>%3$s</h3></a></article>',
                esc_url($item['permalink']),
                esc_url($thumb),
                esc_html($item['title'])
            );
        }

        $output .= '</div></div>';
    }

    $output .= '</div><div class="swiper-pagination"></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>';

    return $output;
});
