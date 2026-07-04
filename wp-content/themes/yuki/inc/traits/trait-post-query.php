<?php

/**
 * Post Query trait
 *
 * @package Yuki
 */
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\QueryTags;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\Query;
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}
if ( !trait_exists( 'Yuki_Post_Query' ) ) {
    /**
     * Post query functions
     */
    trait Yuki_Post_Query
    {
        /**
         * @return array
         */
        protected function getPostsQueryControls() {
            // Get Available Post Types
            $post_types = Query::customPostTypes( 'post', false );
            // Remove WooCommerce Product
            unset($post_types['product']);
            // Get Available Taxonomies
            $post_taxonomies = Query::customPostTypes( 'tax', false );
            $tax_filters = [];
            $tax_filters_controls = [];
            $post_exclude_controls = [];
            $post_manual_controls = [];
            if ( yuki_fs()->is_not_paying() ) {
                $post_exclude_controls[] = ( new Condition() )->setCondition( [
                    'selection' => 'dynamic',
                ] )->setControls( [new Separator(), yuki_upsell_info_control( __( 'More query options in our %sPro Version%s', 'yuki' ) )] );
                $post_manual_controls[] = yuki_upsell_info_control( __( 'Manual selection is only available in our %sPro Version%s', 'yuki' ) );
            }
            foreach ( $post_taxonomies as $slug => $title ) {
                global $wp_taxonomies;
                $post_type = '';
                if ( isset( $wp_taxonomies[$slug] ) && isset( $wp_taxonomies[$slug]->object_type[0] ) ) {
                    $post_type = $wp_taxonomies[$slug]->object_type[0];
                }
                if ( !isset( $tax_filters[$post_type] ) ) {
                    $tax_filters[$post_type] = [];
                }
                $tax_filters[$post_type][$slug] = $title;
            }
            foreach ( $tax_filters as $post_type => $filters ) {
                $controls = [];
                foreach ( $filters as $slug => $title ) {
                    $controls[] = ( new QueryTags('taxonomy_' . $slug) )->setLabel( $title )->setQuey( [
                        'type' => 'taxonomy',
                        'slug' => $slug,
                    ] )->setDefaultValue( [] );
                }
                $tax_filters_controls[] = ( new Condition() )->setCondition( [
                    'source'    => $post_type,
                    'selection' => 'dynamic',
                ] )->setControls( $controls );
            }
            $selections = [
                'dynamic' => __( 'Dynamic', 'yuki' ),
                'manual'  => __( 'Manual', 'yuki' ),
            ];
            if ( yuki_fs()->is_not_paying() ) {
                $selections['manual'] = $selections['manual'] . ' (Pro)';
            }
            return array_merge( [
                ( new Select('source') )->setLabel( __( 'Source', 'yuki' ) )->setDefaultValue( 'post' )->setChoices( $post_types ),
                ( new Radio('selection') )->setLabel( __( 'Selection', 'yuki' ) )->setDefaultValue( 'dynamic' )->buttonsGroupView()->setChoices( $selections ),
                new Separator(),
                ( new Toggle('exclude_no_images') )->setLabel( __( 'Exclude w/o Thumbnail', 'yuki' ) )->closeByDefault(),
                new Separator(),
                ( new Condition() )->setCondition( [
                    'selection' => 'dynamic',
                ] )->setControls( [( new Number('offset') )->setLabel( __( 'Offset', 'yuki' ) )->setMin( 0 )->setMax( 99999 )->setDefaultValue( 0 ), new Separator(), ( new QueryTags('author') )->setLabel( __( 'Author', 'yuki' ) )->setQuey( [
                    'type' => 'user',
                ] )->setDefaultValue( [] )] )->setReverseControls( $post_manual_controls )
            ], $tax_filters_controls, $post_exclude_controls );
        }

        /**
         * @param $posts_per_page
         * @param $settings
         * @param null $page_key
         *
         * @return array
         */
        protected function getPostsQueryArgs( $posts_per_page, $settings, $page_key = null ) {
            $selection = $this->get( 'selection', $settings );
            $source = $this->get( 'source', $settings );
            // Get Paged
            $paged = yuki_get_query_page( $page_key );
            $args = [];
            // Dynamic
            if ( 'dynamic' === $selection ) {
                $author = $this->get( 'author', $settings );
                $offset = absint( $this->get( 'offset', $settings ) );
                $args = [
                    'post_type'      => $source,
                    'tax_query'      => $this->getTaxQueryArgs( $settings ),
                    'posts_per_page' => $posts_per_page,
                    'author'         => ( is_array( $author ) ? implode( ',', $author ) : $author ),
                    'paged'          => $paged,
                    'offset'         => $posts_per_page * ($paged - 1) + $offset,
                ];
            }
            // Manual
            if ( 'manual' === $selection ) {
                if ( yuki_fs()->is_not_paying() ) {
                    return [];
                }
            }
            // Exclude Items without F/Image
            if ( 'yes' === $this->get( 'exclude_no_images', $settings ) ) {
                $args['meta_key'] = '_thumbnail_id';
            }
            return $args;
        }

        /**
         * Taxonomy Query Args
         *
         * @return array|array[]
         */
        protected function getTaxQueryArgs( $settings ) {
            $tax_query = [];
            $source = $this->get( 'source', $settings );
            foreach ( get_object_taxonomies( $source ) as $tax ) {
                $items = $this->get( 'taxonomy_' . $tax, $settings );
                if ( !empty( $items ) ) {
                    array_push( $tax_query, [
                        'taxonomy' => $tax,
                        'field'    => 'id',
                        'terms'    => $items,
                    ] );
                }
            }
            return $tax_query;
        }

    }
}