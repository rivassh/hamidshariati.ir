<?php

/**
 * Post card trait
 *
 * @package Yuki
 */
use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Icons;
use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Text;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Controls\Typography;
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}
if ( !trait_exists( 'Yuki_Post_Pagination' ) ) {
    /**
     * Post card functions
     */
    trait Yuki_Post_Pagination
    {
        /**
         * @return array
         */
        protected function getPaginationControls( $prefix = '', $defaults = [] ) {
            return [( new Tabs() )->setActiveTab( 'content' )->addTab( 'content', __( 'Content', 'yuki' ), $this->getPaginationContentControls( $prefix, $defaults ) )->addTab( 'style', __( 'Style', 'yuki' ), $this->getPaginationStyleControls( $prefix, $defaults ) )];
        }

        /**
         * @return array
         */
        protected function getPaginationContentControls( $prefix = '', $defaults = [] ) {
            $pagination_type = [
                'numbered'        => __( 'Numbered', 'yuki' ),
                'prev-next'       => __( 'Prev & Next', 'yuki' ),
                'load-more'       => __( 'Load More', 'yuki' ),
                'infinite-scroll' => __( 'Infinite Scroll', 'yuki' ),
            ];
            if ( yuki_fs()->is_not_paying() ) {
                $pagination_type['load-more'] = $pagination_type['load-more'] . ' (Pro Only)';
                $pagination_type['infinite-scroll'] = $pagination_type['infinite-scroll'] . ' (Pro Only)';
            }
            $controls = [( new Select($prefix . 'pagination_type') )->setLabel( __( 'Pagination Type', 'yuki' ) )->setDefaultValue( 'numbered' )->setChoices( $pagination_type ), ( new Condition() )->setCondition( [
                $prefix . 'pagination_type' => 'numbered',
            ] )->setControls( [( new Toggle($prefix . 'pagination_prev_next_button') )->setLabel( __( 'Previous & Next Buttons', 'yuki' ) )->openByDefault()] ), ( new Condition() )->setCondition( [
                $prefix . 'pagination_type' => 'numbered|prev-next',
            ] )->setControls( [
                new Separator(),
                ( new Radio($prefix . 'pagination_prev_next_type') )->setLabel( __( 'Previous & Next Type', 'yuki' ) )->setDefaultValue( 'icon' )->buttonsGroupView()->setChoices( [
                    'text' => __( 'Text', 'yuki' ),
                    'icon' => __( 'Icon', 'yuki' ),
                ] ),
                ( new Condition() )->setCondition( [
                    $prefix . 'pagination_prev_next_type' => 'icon',
                ] )->setControls( [( new Icons($prefix . 'pagination_prev_icon') )->setLabel( __( 'Previous Icon', 'yuki' ) )->setDefaultValue( [
                    'value'   => 'fas fa-angle-left',
                    'library' => 'fa-solid',
                ] ), ( new Icons($prefix . 'pagination_next_icon') )->setLabel( __( 'Next Icon', 'yuki' ) )->setDefaultValue( [
                    'value'   => 'fas fa-angle-right',
                    'library' => 'fa-solid',
                ] )] ),
                ( new Condition() )->setCondition( [
                    $prefix . 'pagination_prev_next_type' => 'text',
                ] )->setControls( [( new Text($prefix . 'pagination_prev_text') )->setLabel( __( 'Previous Text', 'yuki' ) )->displayInline()->setDefaultValue( __( 'Prev', 'yuki' ) ), ( new Text($prefix . 'pagination_next_text') )->setLabel( __( 'Next Text', 'yuki' ) )->displayInline()->setDefaultValue( __( 'Next', 'yuki' ) )] ),
                new Separator(),
                ( new Toggle($prefix . 'pagination_disabled_button') )->setLabel( __( 'Show Disabled Buttons', 'yuki' ) )->openByDefault()
            ] )];
            if ( yuki_fs()->is_not_paying() ) {
                $controls[] = ( new Condition() )->setCondition( [
                    $prefix . 'pagination_type' => 'load-more|infinite-scroll',
                ] )->setControls( [new Separator(), yuki_upsell_info_control( __( 'Load More & Infinite Scroll is available in our %sPro Version%s', 'yuki' ) )] );
            }
            $controls[] = new Separator();
            $controls[] = ( new ImageRadio($prefix . 'pagination_alignment') )->setLabel( __( 'Alignment', 'yuki' ) )->inlineChoices()->setDefaultValue( 'flex-start' )->setChoices( [
                'flex-start' => [
                    'src' => yuki_image( 'text-left' ),
                ],
                'center'     => [
                    'src' => yuki_image( 'text-center' ),
                ],
                'flex-end'   => [
                    'src' => yuki_image( 'text-right' ),
                ],
            ] );
            return $controls;
        }

        protected function getPaginationStyleControls( $prefix = '', $defaults = [] ) {
            $controls = [( new Typography($prefix . 'pagination_typography') )->setLabel( __( 'Typography', 'yuki' ) )->setDefaultValue( [
                'family'     => 'inherit',
                'fontSize'   => '0.875rem',
                'variant'    => '400',
                'lineHeight' => '1',
            ] ), new Separator(), ( new Condition() )->setCondition( [
                $prefix . 'pagination_type' => 'numbered|prev-next',
            ] )->setControls( [( new ColorPicker($prefix . 'pagination_button_color') )->setLabel( __( 'Text Color', 'yuki' ) )->addColor( 'initial', __( 'Text Initial', 'yuki' ), 'var(--yuki-accent-active)' )->addColor( 'active', __( 'Text Active', 'yuki' ), 'var(--yuki-base-color)' )->addColor( 'accent', __( 'Accent', 'yuki' ), 'var(--yuki-primary-color)' ), ( new Border($prefix . 'pagination_button_border') )->setLabel( __( 'Border', 'yuki' ) )->displayInline()->setDefaultBorder( 1, 'solid', 'var(--yuki-base-200)' )] )];
            $controls[] = ( new Condition() )->setCondition( [
                $prefix . 'pagination_type' => 'numbered|prev-next|load-more',
            ] )->setControls( [new Separator(), ( new Slider($prefix . 'pagination_button_radius') )->setLabel( __( 'Radius', 'yuki' ) )->enableResponsive()->setDefaultUnit( 'px' )->setDefaultValue( '4px' )->setMin( 0 )->setMax( 100 )] );
            return $controls;
        }

    }
}