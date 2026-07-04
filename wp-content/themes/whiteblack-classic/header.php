<?php
/**
 * The header for WhiteBlack Classic
 *
 *
 * @package WhiteBlack Classic
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" type="text/css" />
<?php wp_head(); ?>
	
</head>
<body <?php body_class();  ?> >
<?php if ( function_exists( 'wp_body_open' ) ) {wp_body_open(); } ?>

	<a class="screen-reader-text2" href="#content"> <?php _e( 'Skip to content', 'whiteblack-classic' ); ?></a>

<div class="colore-3">
		<div class="contenitore-mille giustifica-a-destra">
		<div class="titolo-archivi">
		<?php /* inizio sezione login */ ?>
		<?php if (get_theme_mod('mostra_link_login', false)) : ?>
   		<?php if (!is_user_logged_in()) : ?>
	    <a href="<?php echo wp_login_url(); ?>"><?php _e('Sign in', 'whiteblack-classic'); ?></a>
	    <?php endif; ?>
		<?php endif; ?>
		<?php /* fine sezione login */	?>		
		</div>
		</div>
<div class="contenitore-mille">

<div class="flex-container">
	<div class="flex-item-left">
		<span class="flex-container flex-a-destra">
			<div class="spazio15">
				<?php  the_custom_logo();  ?>
			</div>
			<div>
				<!--	Le classi site-title e site-description
						sono classi di WorddPress.
						Sono richiamate in functions.php e servono per attivare
						il selettore di scelta di visualizzazione del titolo
						e della descrizione del sito nel personalizzatore. -->
				<h2 class="site-title nome-sito-descrizione-sito ombra-testo"><a class="no-decorazione" href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a></h2>
				<p class="site-description nome-sito-descrizione-sito"><?php bloginfo( 'description' ); ?></p>
			</div>
		</span>
	</div>
	<div class="flex-item-right spazio15">
		<div class="blocco a-destra in-colonna">
		<?php 	get_search_form(); ?>

		<?php 
		if ( is_active_sidebar( 'custom-widget-area1' ) ) {
		dynamic_sidebar( 'custom-widget-area1' ); }
		?> 
		</div>		
	</div>
</div>	


<?php	/* -------------inizio menu principale ------------*/ ?>



<?php if ( has_nav_menu( 'primary' ) ) : ?>
<nav class="main-navigation titolo-archivi">
	<button class="menu-toggle">Menu</button>
    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary', 
        'menu_class' => 'menu', 
        'container' => false, 
        'depth' => 4, // Modifica questa parte per aumentare o diminuire la profondità del menu
        'fallback_cb' => false
    ) );
    ?>
</nav>
<?php endif;
	/* -------------fine menu principale ------------*/ ?>

</div>
</div>