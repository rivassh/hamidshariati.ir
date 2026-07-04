<?php
/**
 * The template for displaying the footer
 *
 *
 * @package WhiteBlack Classic
 */
?>

<div class="colore-3">
<div class="contenitore-mille">
<div class="flex-container">
	<div class="flex-item-left nome-sito-descrizione-sito">
		<?php 
		if ( is_active_sidebar( 'custom-widget-area3' ) ) {
		dynamic_sidebar( 'custom-widget-area3' ); }
		?> 
	</div>
	<div class="flex-item-right nome-sito-descrizione-sito">
		<?php 
		if ( is_active_sidebar( 'custom-widget-area4' ) ) {
		dynamic_sidebar( 'custom-widget-area4' ); }
		?>
	</div>
</div>	
</div>

<div class="contenitore-mille">
<div class="flex-container">
	<div class="flex-item-left spazio15">
		<div class="footer-copyright">
		<?php echo esc_textarea(get_theme_mod('copyright_text', '© 2025 Your site. All rights reserved.')); ?>
		</div>		
	</div>
	<div class="flex-item-right spazio15">
		<div>
		<?php _e( 'Powered by ', 'whiteblack-classic' ); ?><a href="https://wordpress.org" target="_blank" rel="nofollow" ><?php _e( 'WordPress', 'whiteblack-classic' ); ?></a>
		</div>
	</div>
</div>	
</div>
</div>

<?php wp_footer();  ?></body>

</html>

