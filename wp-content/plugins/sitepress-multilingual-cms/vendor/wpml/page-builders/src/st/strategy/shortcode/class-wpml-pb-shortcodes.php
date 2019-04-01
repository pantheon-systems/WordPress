<?php

class WPML_PB_Shortcodes {

	/** @var  WPML_PB_Shortcode_Strategy $shortcode_strategy */
	private $shortcode_strategy;

	public function __construct( WPML_PB_Shortcode_Strategy $shortcode_strategy ) {
		$this->shortcode_strategy = $shortcode_strategy;
	}

	public function get_shortcodes( $content ) {

		$shortcodes = array();
		$pattern    = get_shortcode_regex( $this->shortcode_strategy->get_shortcodes() );

		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches ) && isset( $matches[5] ) && ! empty( $matches[5] ) ) {
			for ( $index = 0; $index < sizeof( $matches[0] ); $index ++ ) {
				$shortcode = array(
					'block'      => $matches[0][ $index ],
					'tag'        => $matches[2][ $index ],
					'attributes' => $matches[3][ $index ],
					'content'    => $matches[5][ $index ],
				);

				$nested_shortcodes = array();
				if ( $shortcode['content'] && ! $this->has_regular_text( $shortcode['content'] ) ) {
					$nested_shortcodes = $this->get_shortcodes( $shortcode['content'] );
					if ( count( $nested_shortcodes ) ) {
						$shortcode['content'] = '';
					}
				}

				if ( count( $nested_shortcodes ) ) {
					$shortcodes = array_merge( $shortcodes, $nested_shortcodes );
				}
				$shortcodes[] = $shortcode;
			}
		}

		return $shortcodes;
	}

	private function has_regular_text( $content ) {
		$content_with_stripped_shortcode = preg_replace( '/\[([\S]*)[^\]]*\][\s\S]*\[\/(\1)\]|\[[^\]]*\]/', '', $content );
		$content_with_stripped_shortcode = trim( $content_with_stripped_shortcode );
		return ! empty( $content_with_stripped_shortcode );
	}
}
