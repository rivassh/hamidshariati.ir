<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WhiteBlack_Classic
 */

 get_header(); ?>
<?php /* ============== inizio della pagina ================ */ ?>

<main id="content">

<div class="contenitore-mille">

	<img class="quattrocentoquattro" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/404wordpress.jpg"  alt="<?php _e( 'street lamps', 'whiteblack-classic' ); ?>">

<div class="flex-container">
	<div class="flex-colonna-sinistra colonna-barra">
		<h3>
			<?php _e( '404 Error – The page you requested doesn’t exist.', 'whiteblack-classic' ); ?>
		</h3>
	</div>
	<div class="flex-colonna-destra">
		 <h3>
			 <?php _e( 'The cosmic object you were looking for has disappeared beyond the event horizon. If you arrived at this page using a bookmark or favorites link, please update it accordingly.', 'whiteblack-classic' ); ?>
		</h3>
	</div>
</div>	


</div>		
		
</main>

<?php /* ============== fine della pagina ================ */ ?>
<?php get_footer(); ?>