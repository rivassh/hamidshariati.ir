<?php

/**
 * Yuki Theme Customizer
 *
 * @package Yuki
 */
use LottaFramework\Customizer\CallToActionSection;
use LottaFramework\Facades\CZ;
// customizer elements
require get_template_directory() . '/inc/elements/class-logo-element.php';
require get_template_directory() . '/inc/elements/class-menu-element.php';
require get_template_directory() . '/inc/elements/class-button-element.php';
require get_template_directory() . '/inc/elements/class-divider-element.php';
require get_template_directory() . '/inc/elements/class-socials-element.php';
require get_template_directory() . '/inc/elements/class-search-element.php';
require get_template_directory() . '/inc/elements/class-trigger-element.php';
require get_template_directory() . '/inc/elements/class-collapsable-menu-element.php';
require get_template_directory() . '/inc/elements/class-widgets-element.php';
require get_template_directory() . '/inc/elements/class-theme-switch-element.php';
require get_template_directory() . '/inc/elements/class-copyright-element.php';
require get_template_directory() . '/inc/elements/class-cart-element.php';
// customizer builder
require get_template_directory() . '/inc/builder/class-builder-column.php';
require get_template_directory() . '/inc/builder/class-header-column.php';
require get_template_directory() . '/inc/builder/class-header-row.php';
require get_template_directory() . '/inc/builder/class-footer-column.php';
require get_template_directory() . '/inc/builder/class-footer-row.php';
require get_template_directory() . '/inc/builder/class-modal-row.php';
require get_template_directory() . '/inc/builder/class-header-builder.php';
require get_template_directory() . '/inc/builder/class-footer-builder.php';
// homepage customizer elements
require get_template_directory() . '/inc/homepage/class-magazine-layout.php';
require get_template_directory() . '/inc/homepage/class-posts-base-element.php';
require get_template_directory() . '/inc/homepage/elements/class-heading-element.php';
require get_template_directory() . '/inc/homepage/elements/class-text-element.php';
require get_template_directory() . '/inc/homepage/elements/class-hero-element.php';
require get_template_directory() . '/inc/homepage/elements/class-features-element.php';
require get_template_directory() . '/inc/homepage/elements/class-reviews-element.php';
require get_template_directory() . '/inc/homepage/elements/class-magazine-element.php';
require get_template_directory() . '/inc/homepage/elements/class-posts-grid-element.php';
require get_template_directory() . '/inc/homepage/elements/class-posts-slider-element.php';
require get_template_directory() . '/inc/homepage/elements/class-homepage-widgets-element.php';
// homepage customizer builder
require get_template_directory() . '/inc/homepage/class-homepage-row.php';
require get_template_directory() . '/inc/homepage/class-homepage-column.php';
require get_template_directory() . '/inc/homepage/class-homepage-builder.php';
// customizer sections
require get_template_directory() . '/inc/customizer/class-header-section.php';
require get_template_directory() . '/inc/customizer/class-footer-section.php';
require get_template_directory() . '/inc/customizer/class-homepage-section.php';
require get_template_directory() . '/inc/customizer/class-colors-section.php';
require get_template_directory() . '/inc/customizer/class-global-section.php';
require get_template_directory() . '/inc/customizer/class-archive-section.php';
require get_template_directory() . '/inc/customizer/class-content-section.php';
require get_template_directory() . '/inc/customizer/class-single-post-section.php';
require get_template_directory() . '/inc/customizer/class-pages-section.php';
require get_template_directory() . '/inc/customizer/class-store-notice-section.php';
require get_template_directory() . '/inc/customizer/class-store-catalog-section.php';
/**
 * @param $settings
 *
 * @return void
 * @since v1.3.15
 */
function yuki_update_customizer_default_settings(  $settings = []  ) {
    yuki_update_option( 'customizer_queued_typography', \LottaFramework\Customizer\Controls\Typography::getQueued() );
    yuki_update_option( 'customizer_default_settings', $settings );
    yuki_update_option( 'customizer_default_settings_version', esc_html( yuki_apply_filters( 'customizer_default_settings_version', yuki_get_theme_version() ) ) );
}

/**
 * Get all registered customizer settings.
 *
 * @return array
 *
 * @since 1.3.21
 */
function yuki_cz_settings() {
    $settings = [];
    foreach ( CZ::settings() as $id => $args ) {
        if ( !\LottaFramework\Utils::str_starts_with( $id, 'lotta_rand' ) ) {
            if ( isset( $args['default'] ) ) {
                $settings[$id] = [
                    'default' => $args['default'],
                ];
            }
        }
    }
    return $settings;
}

/**
 * Theme customizer register
 *
 * @param WP_Customize_Manager|null $wp_customize Theme Customizer object.
 */
function yuki_customize_register(  $wp_customize  ) {
    if ( !$wp_customize instanceof WP_Customize_Manager ) {
        $wp_customize = null;
    }
    if ( $wp_customize ) {
        $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
        $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
        if ( isset( $wp_customize->selective_refresh ) ) {
            $wp_customize->selective_refresh->add_partial( 'blogname', array(
                'selector'        => '.site-title a',
                'render_callback' => function () {
                    echo esc_html( get_bloginfo( 'name' ) );
                },
            ) );
            $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
                'selector'        => '.site-tagline',
                'render_callback' => function () {
                    echo esc_html( get_bloginfo( 'description' ) );
                },
            ) );
        }
        $wp_customize->add_section( new CallToActionSection($wp_customize, 'yuki_update_customizer_cache', array(
            'priority' => 99999,
            'title'    => __( 'Update Customizer Cache', 'yuki' ),
            'desc'     => __( 'If the final style is not the same as the preview, please try to update the cache', 'yuki' ),
            'link'     => array(
                'url' => esc_url_raw( add_query_arg( array(
                    'action'   => 'yuki_update_customizer_cache',
                    '_wpnonce' => wp_create_nonce( 'update_customizer_cache' ),
                ), admin_url( 'admin.php' ) ) ),
            ),
        )) );
    }
    // Don't cache woocommerce controls
    if ( YUKI_WOOCOMMERCE_ACTIVE ) {
        if ( $wp_customize ) {
            CZ::changeObject(
                $wp_customize,
                'panel',
                'woocommerce',
                'priority',
                20
            );
            // Remove default catalog columns
            $wp_customize->remove_control( 'woocommerce_catalog_columns' );
        }
        CZ::addSection( $wp_customize, new Yuki_Store_Notice_Section(
            'woocommerce_store_notice',
            __( 'Store Notice', 'yuki' ),
            0,
            'woocommerce'
        ) );
        CZ::addSection( $wp_customize, new Yuki_Store_Catalog_Section(
            'woocommerce_product_catalog',
            __( 'Product Catalog', 'yuki' ),
            0,
            'woocommerce'
        ) );
    }
    $settings_version = yuki_get_option( 'customizer_default_settings_version' );
    $enable_customizer_cache = get_option( 'yuki_enable_customizer_cache', 'yes' );
    if ( !yuki_apply_filters( 'should_reload_customizer_settings', $wp_customize || $enable_customizer_cache === 'no' || yuki_get_theme_version() !== $settings_version ) ) {
        $default_options = yuki_get_option( 'customizer_default_settings', [] );
        if ( !empty( $default_options ) ) {
            CZ::restore( array_merge( $default_options, yuki_cz_settings() ) );
            \LottaFramework\Customizer\Controls\Typography::setQueued( yuki_get_option( 'customizer_queued_typography', [] ) );
            // Manually trigger after register action for builders
            Yuki_Header_Builder::instance()->builder()->do( 'after_register' );
            Yuki_Footer_Builder::instance()->builder()->do( 'after_register' );
            return;
        }
    }
    CZ::addSection( $wp_customize, new Yuki_Header_Section('yuki_header', __( 'Header Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Footer_Section('yuki_footer', __( 'Footer Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Homepage_Section('static_front_page', __( 'Homepage Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Global_Section('yuki_global', __( 'Global Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Colors_Section('yuki_colors', __( 'Colors Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Archive_Section('yuki_archive', __( 'Archive Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Content_Section('yuki_content', __( 'Content Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Single_Post_Section('yuki_single_post', __( 'Single Post Settings', 'yuki' )) );
    CZ::addSection( $wp_customize, new Yuki_Pages_Section('yuki_pages', __( 'Pages Settings', 'yuki' )) );
    /**
     * Cache customizer settings
     */
    $settings = yuki_cz_settings();
    yuki_update_customizer_default_settings( $settings );
    yuki_do_action( 'customizer_default_settings_saved', $settings );
}

add_action( 'customize_register', 'yuki_customize_register' );
add_action( 'yuki_after_lotta_framework_bootstrap', 'yuki_customize_register' );
/**
 * Change customizer localize object
 *
 * @param $localize
 *
 * @return mixed
 */
function yuki_customizer_localize(  $localize  ) {
    $localize['customizer']['colorPicker']['swatches'] = [
        'var(--yuki-primary-color)',
        'var(--yuki-primary-active)',
        'var(--yuki-accent-color)',
        'var(--yuki-accent-active)',
        'var(--yuki-base-300)',
        'var(--yuki-base-200)',
        'var(--yuki-base-100)',
        'var(--yuki-base-color)'
    ];
    return $localize;
}

add_filter( 'lotta_filter_customizer_js_localize', 'yuki_customizer_localize' );