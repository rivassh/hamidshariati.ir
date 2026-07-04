<?php
/**
 * The template for displaying all single posts
 *
 *
 * @package WhiteBlack Classic
 */

get_header();
?>

<main id="content">

    <?php
    while ( have_posts() ) :
        the_post();
?>
<div class="contenitore-mille">

<div class="flex-container">

	<div class="flex-colonna-sinistra">
		<div class"blocco">
			<h2 class="spezzare nome-sito-descrizione-sito colonna-barra" ><?php the_title(); ?></h2>
			<p class="colonna-barra"><?php _e( 'Written on ', 'whiteblack-classic' ); echo get_the_date(); _e( ' by ','whiteblack-classic' ); the_author(); ?></p>
			<p class="colonna-barra">
			<?php echo esc_html__('Categories: ', 'whiteblack-classic');
				  echo get_the_category_list( ', ' ,'', '' ); ?>
			</p>
			<p class="colonna-barra">
			<?php if ( has_tag() ) :  _e( 'Tags: ', 'whiteblack-classic' );
			the_tags( '', ', ', '' );
			endif; ?>
			</p>
			<p class="colonna-barra"><?php edit_post_link(); ?></p>
			
			
			<div class="in-colonna">			
			<?php
				if ( has_post_thumbnail()) {
				/* grab the url for the full size featured image */
				$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
				/* link thumbnail to full size image for use with lightbox*/
				echo '<a href="'.esc_url($featured_img_url).'" rel="lightbox" target="_blank">'; 
				the_post_thumbnail( array( 150, 'auto'),
				array('class' => 'immagine-in-evidenza-loop') ); echo '</a>';
				}
			?>	

			<?php 
			if ( is_active_sidebar( 'custom-widget-area2' ) ) {
			dynamic_sidebar( 'custom-widget-area2' ); }
			?>				
			</div>
		</div>

	</div>

	<div class="flex-colonna-destra">
		<div class"blocco">
			<div class"nome-sito-descrizione-sito">
			<?php the_content(); ?>
			</div>
		</div>
		<div class"blocco">
			<div class"nome-sito-descrizione-sito">
			<?php
			/* If comments are open or we have at least one
			 *  comment, load up the comment template.
			 */
			if ( comments_open() || get_comments_number() ) :
			comments_template();
			endif;
			?>
			</div>
		</div>
	</div>
</div>

</div>
<?php
    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
