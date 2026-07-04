<?php
/**
 * The template for displaying archive pages
 *
 * @package WhiteBlack Classic
 */





 get_header(); ?>
<?php /* ============== inizio della pagina ================ */ ?>

<main id="content">
		
<!-- ----------- inizio del loop ----------- -->	

<div class="contenitore-mille">


<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>></div>
	<?php if ( have_posts() ) : 
            the_archive_title( '<h2 class="titolo-archivi">', '</h2>' );
            the_archive_description( '<h3 class="titolo-archivi">', '</h3>' );
		  while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', 'loop' );
			wp_link_pages();
		  endwhile;
	?>

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

<h3><?php _e( 'No posts found.','whiteblack-classic'); ?></h3>

<?php endif; ?>

</div>		
		
</main>
<!-- ----------- fine del loop ----------- -->

	<?php /* ============== fine della pagina ================ */ ?>
<?php get_footer(); ?>