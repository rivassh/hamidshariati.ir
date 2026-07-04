<?php
/* Template Name: Single column page
 * Template Post Type: page
 * @package WhiteBlack Classic
 *  */ 

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
			
	<div class="nome-sito-descrizione-sito"><?php edit_post_link(); ?><?php the_content(); ?></div>
<?php
        

		// If comments are open or we have at least one comment,
		// load up the comment template.
		if ( comments_open() || get_comments_number() ) :
		comments_template();
		endif;
    endwhile; // End of the loop.
    ?>
</div>
</main><!-- #main -->

<?php
get_footer();

