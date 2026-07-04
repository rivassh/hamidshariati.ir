<?php
/**
 * Template part for displaying page content in index.php, search.php, archive.php
 *
 *
 * @package WhiteBlack Classic
 */
 ?>
<div class="flex-container">
	<div class="flex-colonna-sinistra">
		
			<a class="no-decorazione" href="<?php the_permalink(); ?>"><?php the_title('<h2 class="spezzare nome-sito-descrizione-sito colonna-barra the-title" >','</h2>'); ?></a>

			<p class="colonna-barra"><?php echo get_the_date(); ?></p>
			
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

	
	</div>
	<div class="flex-colonna-destra">
		<div class"blocco">
			<div class"nome-sito-descrizione-sito">
			<?php the_excerpt(); ?>
			</div>
		</div>
	</div>
</div>
