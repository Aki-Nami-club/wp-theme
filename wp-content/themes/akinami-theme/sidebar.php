<?php
/**
 * Sidebar template for AkiNami Theme
 */
?>
<aside id="secondary" class="sidebar widget-area" role="complementary">
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php else : ?>
        <section class="widget widget_text">
            <h2 class="widget-title"><?php esc_html_e( 'Sidebar', 'akinami-theme' ); ?></h2>
            <p><?php esc_html_e( 'Add widgets to this area from the Widgets screen in WordPress admin.', 'akinami-theme' ); ?></p>
        </section>
    <?php endif; ?>
</aside>
