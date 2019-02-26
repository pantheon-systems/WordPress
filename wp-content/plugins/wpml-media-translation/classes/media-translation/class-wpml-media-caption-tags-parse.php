<?php

/**
 * Class WPML_Media_Caption_Tags_Parse
 */
class WPML_Media_Caption_Tags_Parse {

	/**
	 * @param string $text
	 *
	 * @return array
	 */
	public function get_captions( $text ) {
		$captions = array();

		if ( preg_match_all( '/\[caption (.+)\](.+)\[\/caption\]/sU', $text, $matches ) ) {

			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$captions[] = new WPML_Media_Caption( $matches[0][ $i ], $matches[1][ $i ], $matches[2][ $i ] );
			}
		}

		return $captions;
	}

}