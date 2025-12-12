<?php
/**
 * Main template file for AkiNami Theme
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail">
                        <a href="<?php the_permalink(); ?>" rel="bookmark">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
            </header>

            <div class="entry-content">
                <?php the_excerpt(); ?>
            </div>

            <footer class="entry-footer">
                <span class="posted-on"><?php echo get_the_date(); ?></span>
                <span class="byline"><?php the_author_posts_link(); ?></span>
            </footer>
        </article>
        <?php
    endwhile;

    the_posts_navigation();
else :
    ?>
    <article class="no-results not-found">
        <header class="entry-header">
            <h2 class="entry-title"><?php esc_html_e( 'Nothing Found', 'akinami-theme' ); ?></h2>
        </header>
        <div class="entry-content">
            <p><?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'akinami-theme' ); ?></p>
        </div>
    </article>
    <?php
endif;

get_footer();
