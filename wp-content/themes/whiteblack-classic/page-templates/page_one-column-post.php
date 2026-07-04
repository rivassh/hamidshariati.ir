<?php
/* Template Name: Single column post
 * Template Post Type: post
 * @package WhiteBlack Classic
 */ 

get_header();
?>
<main id="content">
<div class="contenitore-mille">
    <?php
    while ( have_posts() ) :
        the_post();
	?>
	<div class="blocco margine15">
	<h2 class="spezzare nome-sito-descrizione-sito ombra" ><?php the_title(); ?></h2>
	</div>
	<div class="spazio15">
		<span><?php _e( 'Written on ', 'whiteblack-classic' ); echo get_the_date(); _e( ' by ','whiteblack-classic' ); the_author(); ?> - </span>
		<span><?php echo esc_html__('Categories: ', 'whiteblack-classic');
	  	echo get_the_category_list( ', ' ,'', '' ); ?> - </span>
		<span><?php if ( has_tag() ) :  _e( 'Tags: ', 'whiteblack-classic' );
		the_tags( '', ', ', '' );
		endif; ?>
		</span>
		<p><?php edit_post_link(); ?> </p>
	</div>
	<div class="nome-sito-descrizione-sito"><?php the_content(); ?></div>

        
<div class="spazio15">
	<?php	// If comments are open or we have at least one comment,
			// load up the comment template.
		if ( comments_open() || get_comments_number() ) :
		comments_template();
		endif;
    endwhile; // End of the loop.
    ?>
</div>
	
</div>
</main><!-- #main -->

<?php
get_footer();

