    </main>
    <footer class="site-footer">
        <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?></p>
        <?php if ( has_nav_menu( 'footer' ) ) : ?>
            <nav class="footer-navigation" aria-label="Footer menu">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'container'      => false,
                ) );
                ?>
            </nav>
        <?php endif; ?>
    </footer>
    <?php wp_footer(); ?>
</body>
</html>
