<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="site-container">
            <div class="header-row header-row--spacer" aria-hidden="true"></div>
            <div class="header-row header-row--branding">
                <div class="site-branding">
                    <?php
                    if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                        the_custom_logo();
                    }
                    ?>
                    <div class="brand-title-group">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title"><?php bloginfo( 'name' ); ?></a>
                        <p class="site-tagline"><?php bloginfo( 'description' ); ?></p>
                    </div>
                </div>
                <div class="header-banner">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/banner-728x90.svg' ); ?>" width="728" height="90" alt="<?php esc_attr_e( 'Site banner', 'akinami-theme' ); ?>">
                </div>
            </div>
            <?php if ( has_nav_menu( 'primary' ) ) : ?>
                <div class="header-row header-row--nav">
                    <nav class="site-navigation" aria-label="Primary menu">
                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_class'     => 'primary-menu',
                            'container'      => false,
                        ) );
                        ?>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main class="site-main site-container layout-sidebar">
