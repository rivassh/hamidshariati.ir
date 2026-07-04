<?php
/**
 * Yuki admin functions
 *
 * @package Yuki
 */

use LottaFramework\Utils;

if ( ! function_exists( 'yuki_recommend_plugins' ) ) {
	function yuki_recommend_plugins() {
		return apply_filters( 'yuki_recommend_plugins', array(
			'plover-kit'   => array(
				'title' => __( 'Plover Kit', 'yuki' ),
				'desc'  => __( 'Plover kit have pluggable modules that enhance the Gutenberg core blocks and also provide extended features.', 'yuki' ),
				'home'  => 'https://wpplover.com/plugins/plover-kit/',
			),
			'kenta-blocks' => array(
				'title' => __( 'Kenta Blocks', 'yuki' ),
				'desc'  => __( 'Kenta Blocks is a set of responsive blocks with powerful options and pre-designed templates library. ', 'yuki' ),
				'home'  => 'https://kentatheme.com/blocks/',
			)
		) );
	}
}

if ( ! function_exists( 'yuki_dismiss_notice' ) ) {
	/**
	 * Dismiss admin notice
	 */
	function yuki_dismiss_notice() {
		global $current_user;

		$user_id = $current_user->ID;

		$dismiss_option = filter_input( INPUT_GET, 'yuki_dismiss', FILTER_UNSAFE_RAW );
		if ( is_string( $dismiss_option ) ) {
			add_user_meta( $user_id, "yuki_dismissed_$dismiss_option", 'true', true );
			// delete_user_meta( $user_id, "yuki_dismissed_$dismiss_option", 'true', true );
			wp_die( '', '', array( 'response' => 200 ) );
		}
	}
}
add_action( 'admin_init', 'yuki_dismiss_notice' );
if ( ! function_exists( 'yuki_premium_child_theme_notice' ) ) {
	/**
	 * Show premium child theme download notice
	 */
	function yuki_premium_child_theme_notice() {
		get_template_part( 'template-parts/admin-premium-child-notice' );
	}
}

// Yuki as parent theme
if ( get_stylesheet() !== 'yuki' && get_stylesheet() !== 'yuki-premium' ) {
	// Not using premium child theme but yuki-premium has been installed
	$premium = wp_get_theme( 'yuki-premium' );
	if ( $premium->exists() && ! Utils::str_ends_with( get_template(), 'premium' ) ) {
		/**
		 * Show download premium child theme notice
		 */
		add_action( 'admin_notices', 'yuki_premium_child_theme_notice' );
	}
}

/**
 * Update dynamic css cache action
 */
if ( ! function_exists( 'yuki_update_customizer_cache_action' ) ) {
	function yuki_update_customizer_cache_action() {

		check_ajax_referer( 'update_customizer_cache' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die(
				'<h1>' . __( 'You need a higher level of permission.', 'yuki' ) . '</h1>' .
				'<p>' . __( 'Sorry, you are not allowed to customize this site.', 'yuki' ) . '</p>',
				403
			);
		}

		yuki_update_customizer_default_settings();
	}
}
add_action( 'admin_action_yuki_update_customizer_cache', 'yuki_update_customizer_cache_action' );

if ( ! function_exists( 'yuki_install_recommend_plugin' ) ) {
	/**
	 * Install Recommend Plugins By One Click
	 */
	function yuki_install_recommend_plugin() {
		check_ajax_referer( 'yuki_install_recommend_plugin' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die(
				'<h1>' . __( 'You need a higher level of permission.', 'yuki' ) . '</h1>' .
				'<p>' . __( 'Sorry, you are not allowed to activate plugins on this site.', 'yuki' ) . '</p>',
				403
			);
		}

		require_once ABSPATH . 'wp-admin/admin-header.php';

		?>
        <div class="wrap">
			<?php

			$slug              = $_GET['slug'] ?? '';
			$recommend_plugins = yuki_recommend_plugins();
			$allowed_plugins   = array_keys( $recommend_plugins );

			if ( ! in_array( $slug, $allowed_plugins ) ) {
				wp_die(
					'<h1>' . __( 'You can not install this plugin.', 'yuki' ) . '</h1>' .
					'<p>' . __( 'Sorry, you are not allowed to install this plugin.', 'yuki' ) . '</p>',
					403
				);
			}

			yuki_do_install_plugins( [
				$slug => $recommend_plugins[ $slug ]['title'],
			], add_query_arg( array( 'page' => 'yuki' ), admin_url( 'themes.php' ) ) );
			?>
        </div>
		<?php
	}
}
add_action( 'admin_action_yuki_install_recommend_plugin', 'yuki_install_recommend_plugin' );
