<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ET_Dashboard_Fonts {

	/**
	 * Returns the list of popular google fonts
	 *
	 */
	function et_get_google_fonts() {
		$google_fonts = array(
			'Open Sans' => array(
				'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
				'character_set' => 'latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Oswald' => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Droid Sans' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Lato' => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,700,700italic,900,900italic',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Open Sans Condensed' => array(
				'styles' 		=> '300,300italic,700',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,greek,vietnamese,cyrillic',
				'type'			=> 'sans-serif',
			),
			'PT Sans' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Ubuntu' => array(
				'styles' 		=> '400,300,300italic,400italic,500,500italic,700,700italic',
				'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
				'type'			=> 'sans-serif',
			),
			'PT Sans Narrow' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Yanone Kaffeesatz' => array(
				'styles' 		=> '400,200,300,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Roboto Condensed' => array(
				'styles' 		=> '400,300,300italic,400italic,700,700italic',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Source Sans Pro' => array(
				'styles' 		=> '400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Nunito' => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Francois One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Roboto' => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic',
				'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Raleway' => array(
				'styles' 		=> '400,100,200,300,600,500,700,800,900',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Arimo' => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin,cyrillic-ext,latin-ext,greek-ext,cyrillic,greek,vietnamese',
				'type'			=> 'sans-serif',
			),
			'Cuprum' => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'sans-serif',
			),
			'Play' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin,cyrillic-ext,cyrillic,greek-ext,greek,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Dosis' => array(
				'styles' 		=> '400,200,300,500,600,700,800',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'sans-serif',
			),
			'Abel' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'sans-serif',
			),
			'Droid Serif' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Arvo' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Lora' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Rokkitt' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'PT Serif' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,cyrillic',
				'type'			=> 'serif',
			),
			'Bitter' => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Merriweather' => array(
				'styles' 		=> '400,300,900,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Vollkorn' => array(
				'styles' 		=> '400,400italic,700italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Cantata One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Kreon' => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Josefin Slab' => array(
				'styles' 		=> '400,100,100italic,300,300italic,400italic,600,700,700italic,600italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Playfair Display' => array(
				'styles' 		=> '400,400italic,700,700italic,900italic,900',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'serif',
			),
			'Bree Serif' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Crimson Text' => array(
				'styles' 		=> '400,400italic,600,600italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Old Standard TT' => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Sanchez' => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Crete Round' => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'serif',
			),
			'Cardo' => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin,greek-ext,greek,latin-ext',
				'type'			=> 'serif',
			),
			'Noticia Text' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin,vietnamese,latin-ext',
				'type'			=> 'serif',
			),
			'Judson' => array(
				'styles' 		=> '400,400italic,700',
				'character_set' => 'latin',
				'type'			=> 'serif',
			),
			'Lobster' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,cyrillic-ext,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Unkempt' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Changa One' => array(
				'styles' 		=> '400,400italic',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Special Elite' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Chewy' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Comfortaa' => array(
				'styles' 		=> '400,300,700',
				'character_set' => 'latin,cyrillic-ext,greek,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Boogaloo' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Fredoka One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Luckiest Guy' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Cherry Cream Soda' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Lobster Two' => array(
				'styles' 		=> '400,400italic,700,700italic',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Righteous' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Squada One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Black Ops One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Happy Monkey' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Passion One' => array(
				'styles' 		=> '400,700,900',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Nova Square' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Metamorphous' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext',
				'type'			=> 'cursive',
			),
			'Poiret One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,latin-ext,cyrillic',
				'type'			=> 'cursive',
			),
			'Bevan' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Shadows Into Light' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'The Girl Next Door' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Coming Soon' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Dancing Script' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Pacifico' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Crafty Girls' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Calligraffitti' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Rock Salt' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Amatic SC' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Leckerli One' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Tangerine' => array(
				'styles' 		=> '400,700',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Reenie Beanie' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Satisfy' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Gloria Hallelujah' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Permanent Marker' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Covered By Your Grace' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Walter Turncoat' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Patrick Hand' => array(
				'styles' 		=> '400',
				'character_set' => 'latin,vietnamese,latin-ext',
				'type'			=> 'cursive',
			),
			'Schoolbell' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
			'Indie Flower' => array(
				'styles' 		=> '400',
				'character_set' => 'latin',
				'type'			=> 'cursive',
			),
		);

		return apply_filters( 'et_google_fonts', $google_fonts );
	}

	/**
	 * Determines a websafe font stack, using font type
	 *
	 */
	function et_get_websafe_font_stack( $type = 'sans-serif' ) {
		$font_stack = '';

		switch ( $type ) {
			case 'sans-serif':
				$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
				break;
			case 'serif':
				$font_stack = 'Georgia, "Times New Roman", serif';
				break;
			case 'cursive':
				$font_stack = 'cursive';
				break;
		}

		return $font_stack;
	}

	/**
	 * Attaches Google Font to given css elements
	 *
	 */
	function et_gf_attach_font( $et_gf_font_name, $elements ) {
		$google_fonts = $this->et_get_google_fonts();
		$output = '';

		$output = sprintf(
			'%s { font-family: "%s", %s; }',
			esc_html( $elements ),
			esc_html( $et_gf_font_name ),
			$this->et_get_websafe_font_stack( $google_fonts[$et_gf_font_name]['type'] )
		);

		return $output;
	}

	/**
	 * Enqueues Google Fonts
	 *
	 */
	function et_gf_enqueue_fonts( $et_gf_font_names ) {
		global $shortname;

		if ( ! is_array( $et_gf_font_names ) || empty( $et_gf_font_names ) ) {
			return;
		}

		$google_fonts = $this->et_get_google_fonts();
		$protocol = is_ssl() ? 'https' : 'http';

		foreach ( $et_gf_font_names as $et_gf_font_name ) {
			$google_font_character_set = $google_fonts[$et_gf_font_name]['character_set'];

			$query_args = array(
				'family' => sprintf( '%s:%s',
					str_replace( ' ', '+', $et_gf_font_name ),
					apply_filters( 'et_gf_set_styles', $google_fonts[$et_gf_font_name]['styles'], $et_gf_font_name )
				),
				'subset' => apply_filters( 'et_gf_set_character_set', $google_font_character_set, $et_gf_font_name ),
			);

			$et_gf_font_name_slug = strtolower( str_replace( ' ', '-', $et_gf_font_name ) );
			wp_enqueue_style( 'et-gf-' . $et_gf_font_name_slug, esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
		}
	}
}