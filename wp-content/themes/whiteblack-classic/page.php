<?php
/**
 * The template for displaying all pages
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
			<p class="colonna-barra"><?php edit_post_link(); ?></p>
		</div>	
		<div class="in-colonna">
			<?php echo the_post_thumbnail( array( 150, 'auto'), array('class' => 'immagine-in-evidenza-loop') ); ?>
		

			<?php 
			if ( is_active_sidebar( 'custom-widget-area2' ) ) {
			dynamic_sidebar( 'custom-widget-area2' ); }
			?>
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
