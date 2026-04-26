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
        'vibemag-fonts',
        'https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'vibemag-tailwind',
        'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
        ['vibemag-fonts'],
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
            'i18n' => [
                'openSearch' => esc_html__('Open search', 'vibemag'),
                'toggleDarkMode' => esc_html__('Toggle dark mode', 'vibemag'),
                'searchPlaceholder' => esc_html__('Search posts...', 'vibemag'),
                'searching' => esc_html__('Searching...', 'vibemag'),
                'noResults' => esc_html__('No results found.', 'vibemag'),
                'untitled' => esc_html__('Untitled', 'vibemag'),
                'latestLabel' => esc_html__('Latest:', 'vibemag'),
                'closeSearch' => esc_html__('Close search', 'vibemag'),
                'sidebarAd' => esc_html__('Sidebar Ad', 'vibemag'),
                'moreStories' => esc_html__('More stories', 'vibemag'),
                'readMore' => esc_html__('Read more', 'vibemag'),
                'footerPages' => esc_html__('Pages', 'vibemag'),
                'footerCategories' => esc_html__('Categories', 'vibemag'),
                'backToTop' => esc_html__('Back to top', 'vibemag'),
                'headerBannerAlt' => esc_html__('Header ad banner', 'vibemag'),
                'sidebarBannerAlt' => esc_html__('Sidebar ad banner', 'vibemag'),
                'wideBannerAlt' => esc_html__('Wide ad banner', 'vibemag'),
            ],
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

add_shortcode('vibemag_category_block', static function (array $atts = []): string {
    $atts = shortcode_atts(
        [
            'category_id' => 0,
            'title'       => '',
            'count'       => 5,
        ],
        $atts,
        'vibemag_category_block'
    );

    $category_id = (int) $atts['category_id'];
    $count       = max(2, (int) $atts['count']);

    if ($category_id <= 0) {
        return '<p>' . esc_html__('Set a valid category_id for vibemag_category_block.', 'vibemag') . '</p>';
    }

    $category = get_category($category_id);

    if (! $category || is_wp_error($category)) {
        return '<p>' . esc_html__('Category not found.', 'vibemag') . '</p>';
    }

    $query = new WP_Query(
        [
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'cat'                 => $category_id,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]
    );

    if (! $query->have_posts()) {
        return '<p>' . esc_html__('No posts in this category yet.', 'vibemag') . '</p>';
    }

    $posts = [];

    while ($query->have_posts()) {
        $query->the_post();
        $posts[] = [
            'title'     => get_the_title(),
            'permalink' => get_permalink(),
            'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
            'date'      => get_the_date(),
        ];
    }

    wp_reset_postdata();

    $heading = $atts['title'] !== '' ? $atts['title'] : $category->name;
    $primary = array_shift($posts);

    $output = '<section class="vibemag-category-block">';
    $output .= sprintf('<h2 class="vibemag-category-block__title">%s</h2>', esc_html($heading));

    $thumb = $primary['thumb'] ?: get_theme_file_uri('assets/images/placeholders/default-post-thumbnail.png');
    $output .= '<article class="vibemag-category-block__featured">';
    $output .= sprintf(
        '<a href="%1$s"><img src="%2$s" alt="%3$s" loading="lazy" /><h3>%3$s</h3><time>%4$s</time></a>',
        esc_url($primary['permalink']),
        esc_url($thumb),
        esc_html($primary['title']),
        esc_html($primary['date'])
    );
    $output .= '</article>';

    if (! empty($posts)) {
        $output .= '<ul class="vibemag-category-block__list">';

        foreach ($posts as $item) {
            $output .= sprintf(
                '<li><a href="%1$s">%2$s</a><time>%3$s</time></li>',
                esc_url($item['permalink']),
                esc_html($item['title']),
                esc_html($item['date'])
            );
        }

        $output .= '</ul>';
    }

    $output .= '</section>';

    return $output;
});



add_shortcode('vibemag_ad_image', static function (array $atts = []): string {
    $atts = shortcode_atts(
        [
            'slot' => 'header',
            'class' => '',
        ],
        $atts,
        'vibemag_ad_image'
    );

    $slot = sanitize_key((string) $atts['slot']);

    $map = [
        'header' => [
            'file' => 'assets/images/banners/ad-header-728x90.png',
            'alt' => esc_html__('Header ad banner', 'vibemag'),
            'width' => 728,
            'height' => 90,
            'class' => 'vibemag-header__ad',
        ],
        'sidebar' => [
            'file' => 'assets/images/banners/ad-sidebar-300x250.png',
            'alt' => esc_html__('Sidebar ad banner', 'vibemag'),
            'width' => 300,
            'height' => 250,
            'class' => 'vibemag-sidebar__ad',
        ],
        'wide' => [
            'file' => 'assets/images/banners/ad-wide-970x250.png',
            'alt' => esc_html__('Wide ad banner', 'vibemag'),
            'width' => 970,
            'height' => 250,
            'class' => 'vibemag-wide-ad__image',
        ],
    ];

    if (! isset($map[$slot])) {
        return '';
    }

    $item = $map[$slot];
    $classes = trim($item['class'] . ' ' . (string) $atts['class']);

    return sprintf(
        '<img class="%1$s" src="%2$s" alt="%3$s" width="%4$d" height="%5$d" loading="lazy" />',
        esc_attr($classes),
        esc_url(get_theme_file_uri($item['file'])),
        esc_attr($item['alt']),
        (int) $item['width'],
        (int) $item['height']
    );
});


add_shortcode('vibemag_sidebar_ad', static function (): string {
    $title = esc_html__('Sidebar Ad', 'vibemag');
    $image = do_shortcode('[vibemag_ad_image slot="sidebar"]');

    return sprintf(
        '<aside class="vibemag-sidebar-widget"><h3>%1$s</h3>%2$s</aside>',
        $title,
        $image
    );
});

add_shortcode('vibemag_wide_ad', static function (): string {
    $image = do_shortcode('[vibemag_ad_image slot="wide"]');

    return '<section class="vibemag-wide-ad">' . $image . '</section>';
});
add_shortcode('vibemag_related_posts', static function (array $atts = []): string {
    if (! is_singular('post')) {
        return '';
    }

    $atts = shortcode_atts(
        [
            'count' => 4,
        ],
        $atts,
        'vibemag_related_posts'
    );

    $count   = max(2, (int) $atts['count']);
    $post_id = get_the_ID();

    if (! $post_id) {
        return '';
    }

    $category_ids = wp_get_post_categories($post_id);

    if (empty($category_ids)) {
        return '';
    }

    $query = new WP_Query(
        [
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'post__not_in'        => [$post_id],
            'category__in'        => $category_ids,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]
    );

    if (! $query->have_posts()) {
        return '';
    }

    $output = '<section class="vibemag-related-posts">';
    $output .= '<h2>' . esc_html__('Related Posts', 'vibemag') . '</h2>';
    $output .= '<div class="vibemag-related-posts__grid">';

    while ($query->have_posts()) {
        $query->the_post();

        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium_large') ?: get_theme_file_uri('assets/images/placeholders/default-post-thumbnail.png');

        $output .= sprintf(
            '<article class="vibemag-related-posts__item"><a href="%1$s"><img src="%2$s" alt="%3$s" loading="lazy" /><h3>%3$s</h3></a></article>',
            esc_url(get_permalink()),
            esc_url($thumb),
            esc_html(get_the_title())
        );
    }

    $output .= '</div></section>';

    wp_reset_postdata();

    return $output;
});
