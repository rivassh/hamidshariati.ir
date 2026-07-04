<?php
/**
 * The template for displaying search results pages
 *
 *
 * @package WhiteBlack Classic
 */


 get_header(); ?>
<?php /* ============== inizio della pagina ================ */ ?>

<main id="content">
		
<!-- ----------- inizio del loop ----------- -->	

<div class="contenitore-mille">


<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>></div>
<?php if ( have_posts() ) : ?>
		<header>
            <h2 class="titolo-archivi">
                <?php
                printf(
                    /* translators: %s: search query. */
                    esc_html__( 'Search results for: %s', 'whiteblack-classic' ),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h2>
        </header>
<?php while ( have_posts() ) : the_post();
get_template_part( 'template-parts/content', 'loop' );
wp_link_pages();
endwhile; ?>

<div class="flex-paginazione">
	<div class="paginazione-sinistra">
		<?php if ( get_previous_posts_link() ) {
				previous_posts_link();
				}
		?>
	</div>
	<div class="paginazione-destra">
		<?php if ( get_next_posts_link() ) {
				next_posts_link();
				}
		?>
	</div>
</div>


<?php else: ?>
<h3><?php _e( 'No result found.','whiteblack-classic'); ?></h3>

<?php endif; ?>

</div>		
		
</main>
<!-- ----------- fine del loop ----------- -->

	<?php /* ============== fine della pagina ================ */ ?>
<?php get_footer(); ?>