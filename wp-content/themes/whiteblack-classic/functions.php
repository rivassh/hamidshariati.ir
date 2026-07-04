<?php
/**
 * WhiteBlack Classic functions and definitions
 *
 *
 * @package WhiteBlack Classic
 */
 
/* ---------------- inizio add theme support ------------------*/
if ( ! function_exists( 'whiteblack_classic_add_theme_supports' ) ) :

function whiteblack_classic_add_theme_supports() {
	add_theme_support( "title-tag" ); 
	add_editor_style();
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'block-templates' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo',
						array(
							'height'				=> 100,
							'width'					=> 100,
							'flex-height'			=> true,
							'flex-width'			=> false,
							'header-text'			=> array(
														'site-title',
														'site-description' ),
							'unlink-homepage-logo'	=> false,
						)
					);
	add_theme_support( 'html5', array(
									'comment-list',
									'comment-form',
									'search-form',
									'gallery',
									'caption',
									'style',
									'script'
									)
					 );
	
	// colori dell'editor
	$coloreuno      = '#333333';
	$coloredue      = '#999999';
	$coloretre      = '#cccccc';
	$colorequattro  = '#eeeeee';
	$colorecinque   = '#cf2e2e';
	$black     		= '#000000';
	$cyanbluishgray = '#abb8c3';
	$white     		= '#FFFFFF';
	$palepink      	= '#f78da7';
	$vividred     	= '#cf2e2e';
	$orange  		= '#ff6900';
	$amber    		= '#fcb900';
	$lightgreencyan = '#7bdcb5';
	$vividgreencyan = '#00d084';
	$palecyanblue   = '#8ed1fc';
	$vividcyanblue  = '#0693e3';
	$vividpurple	= '#9b51e0';

	
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => esc_html__( 'Color 1', 'whiteblack-classic' ),
				'slug'  => 'coloreuno',
				'color' => $coloreuno,
			),
			array(
				'name'  => esc_html__( 'Color 2', 'whiteblack-classic' ),
				'slug'  => 'coloredue',
				'color' => $coloredue,
			),
			array(
				'name'  => esc_html__( 'Color 3', 'whiteblack-classic' ),
				'slug'  => 'coloretre',
				'color' => $coloretre,
			),
			array(
				'name'  => esc_html__( 'Color 4', 'whiteblack-classic' ),
				'slug'  => 'colorequattro',
				'color' => $colorequattro,
			),
				array(
					'name'  => esc_html__( 'Color 5', 'whiteblack-classic' ),
					'slug'  => 'colorecinque',
					'color' => $colorecinque,
				),
			array(
				'name'  => esc_html__( 'Black', 'whiteblack-classic' ),
				'slug'  => 'black',
				'color' => $black,
			),
			array(
				'name'  => esc_html__( 'Cyan bluish gray', 'whiteblack-classic' ),
				'slug'  => 'cyanbluishgray',
				'color' => $cyanbluishgray,
			),
			array(
				'name'  => esc_html__( 'White', 'whiteblack-classic' ),
				'slug'  => 'white',
				'color' => $white,
			),
			array(
				'name'  => esc_html__( 'Pale pink', 'whiteblack-classic' ),
				'slug'  => 'pale pink',
				'color' => $palepink,
			),
			array(
				'name'  => esc_html__( 'Vivid red', 'whiteblack-classic' ),
				'slug'  => 'vividred',
				'color' => $vividred,
			),
			array(
				'name'  => esc_html__( 'Orange', 'whiteblack-classic' ),
				'slug'  => 'orange',
				'color' => $orange,
			),
			array(
				'name'  => esc_html__( 'Amber', 'whiteblack-classic' ),
				'slug'  => 'amber',
				'color' => $amber,
			),
			array(
				'name'  => esc_html__( 'Light green cyan', 'whiteblack-classic' ),
				'slug'  => 'ightgreencyan',
				'color' => $lightgreencyan,
			),
			array(
				'name'  => esc_html__( 'Vivid green cyan', 'whiteblack-classic' ),
				'slug'  => 'vividgreencyan',
				'color' => $vividgreencyan,
			),
			array(
				'name'  => esc_html__( 'Pale cyan blue', 'whiteblack-classic' ),
				'slug'  => 'palecyanblue',
				'color' => $palecyanblue,
			),
			array(
				'name'  => esc_html__( 'Vivid cyan blue', 'whiteblack-classic' ),
				'slug'  => 'vividcyanblue',
				'color' => $vividcyanblue,
			),
			array(
				'name'  => esc_html__( 'Vivid purple', 'whiteblack-classic' ),
				'slug'  => 'vividpurple',
				'color' => $vividpurple,
			)
			)
		);

	
	
	
}
endif; // whiteblack_classic_add_theme_supports
add_action( 'after_setup_theme', 'whiteblack_classic_add_theme_supports' );

/* ---------------- fine add theme support ------------------*/


function whiteblack_classic_enqueue_comment_reply_script() {
    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'whiteblack_classic_enqueue_comment_reply_script');



function whiteblack_classic_register_custom_widget_area() {
    register_sidebar(
        array(
            'name'          => __( 'Widget Area 1', 'whiteblack-classic' ),
            'id'            => 'custom-widget-area1',
            'description'   => __( 'Add widgets here to appear in your custom area.', 'whiteblack-classic' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
	register_sidebar(
        array(
            'name'          => __( 'Widget Area 2', 'whiteblack-classic' ),
            'id'            => 'custom-widget-area2',
            'description'   => __( 'Add widgets here to appear in your custom area.', 'whiteblack-classic' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
	register_sidebar(
        array(
            'name'          => __( 'Widget Area 3', 'whiteblack-classic' ),
            'id'            => 'custom-widget-area3',
            'description'   => __( 'Add widgets here to appear in your custom area.', 'whiteblack-classic' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
	register_sidebar(
        array(
            'name'          => __( 'Widget Area 4', 'whiteblack-classic' ),
            'id'            => 'custom-widget-area4',
            'description'   => __( 'Add widgets here to appear in your custom area.', 'whiteblack-classic' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'whiteblack_classic_register_custom_widget_area' );


/* registrazione dei menu nel tema: si possono
 * registrare più posizione dei menu nel tema  */

function whiteblack_classic_my_theme_setup() {
    // Registrazione del menu
    register_nav_menus(array(
		'primary' => __('Responsive menu', 'whiteblack-classic'),
    ));
}
add_action('after_setup_theme', 'whiteblack_classic_my_theme_setup');

/* fine registrazione dei menu nel tema */


/* -------------Registrazione percorso del file javascript---------------------------- */
function whiteblack_classic_enqueue_scripts() {
    wp_enqueue_script( 'stg-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0', true ); 
}
add_action( 'wp_enqueue_scripts', 'whiteblack_classic_enqueue_scripts' );
/* -----------------Fine registrazione del file javascript ----------------------------*/


/*
 * ======= Inizio blocco effetti e pagina di benvenuto ======
 * Register block styles
 * Register custom welcome page
 * Theme: WhiteBlack Classic
 */
function whiteblack_classic_register_block_styles() {
	$blocks = array( 'image', 'post-featured-image' );

	foreach( $blocks as $block ) {

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-grayscale',
				'label'        => __( 'Gray', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-grayscale{ filter: grayscale(100%); }',
			)
		);
	
		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-blur',
				'label'        => __( 'Blur', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-blur{ filter: blur(5px); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-brightness',
				'label'        => __( 'Brightness', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-brightness{ filter: brightness(200%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-contrast',
				'label'        => __( 'Contrast', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-contrast{ filter: contrast(200%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-hue-90',
				'label'        => __( 'Hue90', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-hue-90{ filter: hue-rotate(90deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-hue-180',
				'label'        => __( 'Hue180', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-hue-180{ filter: hue-rotate(180deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-hue-270',
				'label'        => __( 'Hue270', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-hue-270{ filter: hue-rotate(270deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-invert',
				'label'        => __( 'Invert', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-invert{ filter: invert(100%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-opacity',
				'label'        => __( 'Opacity', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-opacity{ filter: opacity(30%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-saturate',
				'label'        => __( 'Saturate', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-saturate{ filter: saturate(8); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-sepia',
				'label'        => __( 'Sepia', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-sepia{ filter: sepia(100%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromgray',
				'label'        => __( 'Gray →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromgray { filter: grayscale(100%); } .is-style-whiteblack-fromgray:hover { filter: grayscale(0%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-togray',
				'label'        => __( '→ Gray', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-togray:hover { filter: grayscale(100%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromblur',
				'label'        => __( 'Blur →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromblur { filter: blur(5px); } .is-style-whiteblack-fromblur:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-toblur',
				'label'        => __( '→ Blur', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-toblur:hover { filter: blur(5px); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-frombrightness',
				'label'        => __( 'Brightness →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-frombrightness { filter: brightness(200%); } .is-style-whiteblack-frombrightness:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tobrightness',
				'label'        => __( '→ Brightness', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tobrightness:hover { filter: brightness(200%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromcontrast',
				'label'        => __( 'Contrast →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromcontrast { filter: contrast(200%); } .is-style-whiteblack-fromcontrast:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tocontrast',
				'label'        => __( '→ Contrast', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tocontrast:hover { filter: contrast(200%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromhue90',
				'label'        => __( 'Hue90 →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromhue90 { filter: hue-rotate(90deg); } .is-style-whiteblack-fromhue90:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tohue90',
				'label'        => __( '→ Hue90', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tohue90:hover { filter: hue-rotate(90deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromhue180',
				'label'        => __( 'Hue180 →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromhue180 { filter: hue-rotate(180deg); } .is-style-whiteblack-fromhue180:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tohue180',
				'label'        => __( '→ Hue180', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tohue180:hover { filter: hue-rotate(180deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromhue270',
				'label'        => __( 'Hue270 →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromhue270 { filter: hue-rotate(270deg); } .is-style-whiteblack-fromhue270:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tohue270',
				'label'        => __( '→ Hue270', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tohue270:hover { filter: hue-rotate(270deg); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-frominvert',
				'label'        => __( 'Invert →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-frominvert { filter: invert(100%); } .is-style-whiteblack-frominvert:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-toinvert',
				'label'        => __( '→ Invert', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-toinvert:hover { filter: invert(100%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromopacity',
				'label'        => __( 'Opacity →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromopacity { filter: opacity(30%); } .is-style-whiteblack-fromopacity:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-toopacity',
				'label'        => __( '→ Opacity', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-toopacity:hover { filter: opacity(30%); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromsaturate',
				'label'        => __( 'Saturate →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromsaturate { filter: saturate(8); } .is-style-whiteblack-fromsaturate:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tosaturate',
				'label'        => __( '→ Saturate', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tosaturate:hover { filter: saturate(8); }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-fromsepia',
				'label'        => __( 'Sepia →', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-fromsepia { filter: sepia(100%); } .is-style-whiteblack-fromsepia:hover { filter: none; }',
			)
		);

		register_block_style(
			'core/' . $block,
			array(
				'name'         => 'whiteblack-tosepia',
				'label'        => __( '→ Sepia', 'whiteblack-classic' ),
				'inline_style' => '.is-style-whiteblack-tosepia:hover { filter: sepia(100%); }',
			)
		);

	}
}
add_action( 'init', 'whiteblack_classic_register_block_styles' );


function whiteblack_classic_wp_custom_welcome_page() {
	 include(get_template_directory() .'/assets/benvenuto/benvenuto.php');
}


function whiteblack_classic_wp_custom_welcome_menu() {
    add_menu_page(
        __( 'Welcome', 'whiteblack-classic' ),
        __( 'Welcome', 'whiteblack-classic' ),
        'edit_posts', /* min Author role */
        'custom-welcome',
        'whiteblack_classic_wp_custom_welcome_page',
        'dashicons-welcome-learn-more',
        3
    );
}
add_action( 'admin_menu', 'whiteblack_classic_wp_custom_welcome_menu' );

/*
 * Register block styles
 * Register custom welcome page
 * Theme: WhiteBlack Classic
 * ======= Fine blocco effetti e pagina di benvenuto ======
 */

/*------------------------form per copyright nel personalizzatore------------------------*/
function whiteblack_classic_aggiungi_campo_copyright_customizer($wp_customize) {
    // Aggiungi una sezione per il copyright
    $wp_customize->add_section('copyright_section', array(
        'title'       => __('Copyright', 'whiteblack-classic'),
        'description' => __('Customize the copyright text in the footer', 'whiteblack-classic'),
        'priority'    => 120,
    ));

    // Aggiungi il campo per il testo del copyright
    $wp_customize->add_setting('copyright_text', array(
        'default' => '© 2025 Your site. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('copyright_text', array(
        'label'    => __('Copyright text - maximum 100 characters', 'whiteblack-classic'),
        'section'  => 'copyright_section',
        'settings' => 'copyright_text',
        'type'     => 'text',
        'input_attrs' => array(
            'maxlength' => 100, // Limita il numero di caratteri a 100
        ),
    ));

    // Aggiungi il JavaScript per limitare i caratteri
    $wp_customize->get_setting('copyright_text')->transport = 'postMessage'; // Forza il live-preview

    add_action('customize_controls_print_footer_scripts', function() {
        ?>
        <script type="text/javascript">
            (function($) {
                // Imposta il limite di caratteri
                var maxChars = 100;
                $('#copyright_text').on('input', function() {
                    var currentLength = $(this).val().length;
                    if (currentLength > maxChars) {
                        $(this).val($(this).val().substring(0, maxChars)); // Troncamento se supera il limite
                    }
                });
            })(jQuery);
        </script>
        <?php
    });
}
add_action('customize_register', 'whiteblack_classic_aggiungi_campo_copyright_customizer');

function whiteblack_classic_validate_copyright_text($value) {
    // Imposta il limite di caratteri
    $max_length = 100;
    
    // Troncare il testo se supera la lunghezza massima
    if (strlen($value) > $max_length) {
        $value = substr($value, 0, $max_length);
    }

    return $value;
}
add_filter('pre_update_option_copyright_text', 'whiteblack_classic_validate_copyright_text');
/*------------------------fine form per copyright nel personalizzatore-------------------*/

/*-------------------------inizio selettore login nel personalizzatore-------------------*/
function whiteblack_classic_aggiungi_opzione_login_personalizzatore($wp_customize) {
    // Aggiungere una sezione per l'header
    $wp_customize->add_section('sezione_login', array(
        'title' => __('Login option', 'whiteblack-classic'),
        'priority' => 30,
    ));

    // Aggiungere una impostazione per il link di login
    $wp_customize->add_setting('mostra_link_login', array(
        'default' => false, // Impostazione predefinita (nasconde il link)
        'transport' => 'refresh', // Ricarica la pagina quando cambia il valore
		'sanitize_callback' => 'esc_url_raw' //cleans URL from all invalid characters
    ));

    // Aggiungere un controllo per mostrare/nascondere il link
    $wp_customize->add_control('mostra_link_login_control', array(
        'label' => __('Show login link', 'whiteblack-classic'),
        'section' => 'sezione_login',
        'settings' => 'mostra_link_login',
        'type' => 'checkbox', // Tipo di input: checkbox
    ));
}

add_action('customize_register', 'whiteblack_classic_aggiungi_opzione_login_personalizzatore');

/*-------------------------fine selettore login nel personalizzatore---------------------*/


/**
 * Inizio - Filter the "read more" excerpt string link to the post.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function whiteblack_classic_excerpt_more( $more ) {
	if ( ! is_single() ) {
		$more = sprintf( '<a href="%1$s">%2$s</a>',
			get_permalink( get_the_ID() ),
			__( ' ...read more.', 'whiteblack-classic' )
		);
	}

	return $more;
}
add_filter( 'excerpt_more', 'whiteblack_classic_excerpt_more' );

/* Fine - Filter the "read more" excerpt string link to the post. */

/* ---------------- Pattern Canittu Bau ------------------------------*/
function whiteblack_classic_block_pattern() {
  register_block_pattern(
  'whiteblack-classic/countyevent',
  	array(
		'title'      => __('County event', 'whiteblack-classic'),
		'description'  => __('Poster for a conference on companion dogs.', 'whiteblack-classic'),
		'categories' => array('banner','text'),
		'inserter' => 'true',
		'keywords' => array('county', 'event', 'text', 'banner',),
		'viewPortWidth' => '500',
		'content'    => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"15px","right":"15px"}}},"backgroundColor":"custom-color-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-custom-color-1-background-color has-background" style="padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px"><!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-4"}}},"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"}},"border":{"width":"1px"}},"backgroundColor":"custom-color-1","textColor":"custom-color-4","borderColor":"custom-color-4","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-custom-color-4-border-color has-custom-color-4-color has-custom-color-1-background-color has-text-color has-background has-link-color" style="border-width:1px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:site-title {"textAlign":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-4"}}}},"textColor":"custom-color-4"} /-->

<!-- wp:heading {"textAlign":"center","style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-4"}}}},"textColor":"custom-color-4","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-custom-color-4-color has-text-color has-link-color has-x-large-font-size">' . __('Event of the week', 'whiteblack-classic' ) . '</h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"15px","right":"15px"}}},"backgroundColor":"custom-color-4","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-custom-color-4-background-color has-background" style="padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px"><!-- wp:spacer {"height":"50px"} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":"48px"},"elements":{"link":{"color":{"text":"var:preset|color|custom-color-1"}}}},"textColor":"custom-color-1"} -->
<p class="has-text-align-right has-custom-color-1-color has-text-color has-link-color" style="font-size:48px"><strong>' . __('WALLA WALLA EVENT', 'whiteblack-classic' ) . '</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"48px"},"elements":{"link":{"color":{"text":"var:preset|color|custom-color-2"}}}},"textColor":"custom-color-2"} -->
<p class="has-text-align-center has-custom-color-2-color has-text-color has-link-color" style="font-size:48px"><strong>' . __('WALLA WALLA EVENT', 'whiteblack-classic' ) . '</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"48px"},"elements":{"link":{"color":{"text":"var:preset|color|custom-color-3"}}}},"textColor":"custom-color-3"} -->
<p class="has-custom-color-3-color has-text-color has-link-color" style="font-size:48px"><strong>' . __('WALLA WALLA EVENT', 'whiteblack-classic' ) . '</strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">' . __('The highly esteemed Professor Canittu Bau will hold a conference entitled "Our beloved dog friends" dedicated to all the friends of our four-legged friends', 'whiteblack-classic' ) . '</h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"25px"} -->
<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"15px","bottom":"15px"}}},"backgroundColor":"custom-color-2"} -->
<hr class="wp-block-separator has-text-color has-custom-color-2-color has-alpha-channel-opacity has-custom-color-2-background-color has-background" style="margin-top:15px;margin-bottom:15px"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":4,"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-1"}}}},"textColor":"custom-color-1"} -->
<h4 class="wp-block-heading has-custom-color-1-color has-text-color has-link-color">' . __('The event will take place in the Walla Walla University Conference Room, Friday, October 14, at 10:00 PM', 'whiteblack-classic' ) . '</h4>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"15px","right":"15px"}}},"backgroundColor":"custom-color-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-custom-color-1-background-color has-background" style="padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px"><!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-2"}}},"spacing":{"padding":{"right":"10px","left":"10px","top":"5px","bottom":"5px"}},"border":{"width":"1px","color":"#eeeeee"}},"backgroundColor":"custom-color-1","textColor":"custom-color-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-border-color has-custom-color-2-color has-custom-color-1-background-color has-text-color has-background has-link-color" style="border-color:#eeeeee;border-width:1px;padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:10px"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-4"}}}},"textColor":"custom-color-4","fontSize":"small"} -->
<p class="has-custom-color-4-color has-text-color has-link-color has-small-font-size">' . __('Address: 149B - Pinocchietti Marcellini avenue, East Riverside.', 'whiteblack-classic' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-color-4"}}}},"textColor":"custom-color-4","fontSize":"small"} -->
<p class="has-custom-color-4-color has-text-color has-link-color has-small-font-size">' . __('For further information, please contact the University from 9am to 11am every weekday.', 'whiteblack-classic' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->',	

	)
);
}

add_action( 'init', 'whiteblack_classic_block_pattern' );

/* ----------------Fine pattern Canittu Bau ------------------------------*/
