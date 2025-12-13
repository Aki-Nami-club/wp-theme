<?php
/**
 * AkiNami Theme functions and definitions
 */

if ( ! defined( 'AKINAMI_THEME_VERSION' ) ) {
    define( 'AKINAMI_THEME_VERSION', '1.0.0' );
}

if ( ! function_exists( 'akinami_theme_setup' ) ) {
    /**
     * Set up theme defaults and register WordPress features.
     */
    function akinami_theme_setup() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'custom-logo', array(
            'height'      => 64,
            'width'       => 64,
            'flex-height' => true,
            'flex-width'  => true,
        ) );
        add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'akinami-theme' ),
            'footer'  => __( 'Footer Menu', 'akinami-theme' ),
        ) );
    }
}
add_action( 'after_setup_theme', 'akinami_theme_setup' );

if ( ! function_exists( 'akinami_theme_widgets_init' ) ) {
    /**
     * Register widget area.
     */
    function akinami_theme_widgets_init() {
        register_sidebar( array(
            'name'          => __( 'Sidebar', 'akinami-theme' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here to appear in your sidebar.', 'akinami-theme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );
    }
}
add_action( 'widgets_init', 'akinami_theme_widgets_init' );

if ( ! function_exists( 'akinami_theme_scripts' ) ) {
    /**
     * Enqueue theme styles.
     */
    function akinami_theme_scripts() {
        wp_enqueue_style( 'akinami-theme-style', get_stylesheet_uri(), array(), AKINAMI_THEME_VERSION );
    }
}
add_action( 'wp_enqueue_scripts', 'akinami_theme_scripts' );
