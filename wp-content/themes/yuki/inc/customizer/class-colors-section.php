<?php

/**
 * Colors customizer section
 *
 * @package Yuki
 */
use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\ColorPalettes;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Placeholder;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Section;
use LottaFramework\Facades\AsyncCss;
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}
if ( !class_exists( 'Yuki_Colors_Section' ) ) {
    class Yuki_Colors_Section extends Section {
        /**
         * {@inheritDoc}
         */
        public function getControls() {
            return [
                ( new Tabs('yuki_color_scheme') )->setActiveTab( 'light' )->addTab( 'light', __( 'Light', 'yuki' ), $this->getColors( 'light', yuki_light_color_presets() ) )->addTab( 'dark', __( 'Dark', 'yuki' ), $this->getColors( 'dark', yuki_dark_color_presets() ) ),
                ( new Radio('yuki_default_color_scheme') )->setLabel( __( 'Default Scheme', 'yuki' ) )->setDefaultValue( 'light' )->buttonsGroupView()->setChoices( [
                    'light' => __( 'Light', 'yuki' ),
                    'dark'  => __( 'Dark', 'yuki' ),
                ] ),
                ( new \LottaFramework\Customizer\Controls\Toggle('yuki_save_color_scheme') )->setLabel( __( 'Save User Color Scheme', 'yuki' ) )->setDescription( __( "Save the user's color scheme to the cookie and refresh the page without losing current color scheme.", 'yuki' ) )->openByDefault(),
                new Separator('yuki_site_background_separator'),
                ( new Background('yuki_site_background') )->setLabel( __( 'Site Background', 'yuki' ) )->asyncCss( '.yuki-body', AsyncCss::background() )->enableResponsive()->setDefaultValue( [
                    'type'  => 'color',
                    'color' => 'var(--yuki-base-100)',
                ] )
            ];
        }

        public function getColors( $scheme, $palettes = [] ) {
            $palettesControl = ( new ColorPalettes("yuki_{$scheme}_color_palettes", [
                "yuki-{$scheme}-primary-color"   => __( 'Primary Color', 'yuki' ),
                "yuki-{$scheme}-primary-active"  => __( 'Primary Active', 'yuki' ),
                "yuki-{$scheme}-primary-content" => __( 'Primary Content', 'yuki' ),
                "yuki-{$scheme}-accent-color"    => __( 'Accent Color', 'yuki' ),
                "yuki-{$scheme}-accent-active"   => __( 'Accent Active', 'yuki' ),
                "yuki-{$scheme}-accent-content"  => __( 'Accent Content', 'yuki' ),
                "yuki-{$scheme}-base-color"      => __( 'Base Color', 'yuki' ),
                "yuki-{$scheme}-base-100"        => __( 'Base 100', 'yuki' ),
                "yuki-{$scheme}-base-200"        => __( 'Base 200', 'yuki' ),
                "yuki-{$scheme}-base-300"        => __( 'Base 300', 'yuki' ),
            ]) )->setLabel( __( 'Color Presets', 'yuki' ) )->setColor( "yuki_{$scheme}_primary_color", [
                'default' => "yuki-{$scheme}-primary-color",
                'active'  => "yuki-{$scheme}-primary-active",
            ] )->setColor( "yuki_{$scheme}_accent_color", [
                'default' => "yuki-{$scheme}-accent-color",
                'active'  => "yuki-{$scheme}-accent-active",
            ] )->setColor( "yuki_{$scheme}_base_color", [
                'default' => "yuki-{$scheme}-base-color",
                '100'     => "yuki-{$scheme}-base-100",
                '200'     => "yuki-{$scheme}-base-200",
                '300'     => "yuki-{$scheme}-base-300",
            ] )->bindSelectiveRefresh( 'yuki-global-selective-css' )->setDefaultValue( apply_filters( "yuki_default_{$scheme}_palette", 'palette-1' ) );
            foreach ( $palettes as $index => $palette ) {
                $palettesControl->addPalette( 'palette-' . ($index + 1), $palette );
            }
            $controls = [$palettesControl];
            $controls = array_merge( $controls, [
                ( new Placeholder("yuki_{$scheme}_primary_color") )->displayRaw()->setCustomizerColors( ':root', [
                    'default' => "--yuki-{$scheme}-primary-color",
                    'active'  => "--yuki-{$scheme}-primary-active",
                ] )->addColor( 'default', "var(--yuki-{$scheme}-primary-color)" )->addColor( 'active', "var(--yuki-{$scheme}-primary-active)" ),
                ( new Placeholder("yuki_{$scheme}_accent_color") )->displayRaw()->setCustomizerColors( ':root', [
                    'default' => "--yuki-{$scheme}-accent-color",
                    'active'  => "--yuki-{$scheme}-accent-active",
                ] )->addColor( 'default', "var(--yuki-{$scheme}-accent-color)" )->addColor( 'active', "var(--yuki-{$scheme}-accent-active)" ),
                ( new Placeholder("yuki_{$scheme}_base_color") )->displayRaw()->setCustomizerColors( ':root', [
                    'default' => "--yuki-{$scheme}-base-color",
                    '100'     => "--yuki-{$scheme}-base-100",
                    '200'     => "--yuki-{$scheme}-base-200",
                    '300'     => "--yuki-{$scheme}-base-300",
                ] )->addColor( '300', "var(--yuki-{$scheme}-base-300)" )->addColor( '200', "var(--yuki-{$scheme}-base-200)" )->addColor( '100', "var(--yuki-{$scheme}-base-100)" )->addColor( 'default', "var(--yuki-{$scheme}-base-color)" ),
                new Separator(),
                yuki_upsell_info_control( __( 'Fully customize your colors in %sPro Version%s', 'yuki' ) )
            ] );
            return $controls;
        }

    }

}