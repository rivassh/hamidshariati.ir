<?php
/**
 * Homepage customizer section
 *
 * @package Yuki
 */

use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Info;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Section as CustomizerSection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Yuki_Homepage_Section' ) ) {

	class Yuki_Homepage_Section extends CustomizerSection {

		public function getControls() {
			$controls = [
				( new Separator( 'yuki_homepage_separator' ) ),
				( new Toggle( 'yuki_show_frontpage_header' ) )
					->setLabel( __( 'Show Page Header', 'yuki' ) )
					->closeByDefault()
				,
			];

			if ( ! yuki_is_homepage_builder_disabled() ) {
				$controls[] =
					( new Section( 'yuki_homepage_builder_section' ) )
						->setLabel( __( 'Homepage Builder', 'yuki' ) )
						->enableSwitch()
						->keepMarginAbove()
						->setControls( [
							Yuki_Homepage_Builder::instance()
							                     ->builder()
							                     ->setPreviewLocation( $this->id . ':yuki_homepage_builder_section' )
						] );
			}

			return [
				( new Condition( 'yuki_show_homepage_builder' ) )
					->setCondition( [
						'show_on_front' => 'page',
						'page_on_front' => '!0'
					] )
					->setReverseControls( [
						( new Info( 'yuki_homepage_builder_info' ) )
							->setInfo( __( 'You need to set up a static homepage to enable the homepage builder.', 'yuki' ) )
							->alignCenter()
							->showBackground()
						,
					] )
					->setControls( $controls )
				,
			];
		}
	}
}


