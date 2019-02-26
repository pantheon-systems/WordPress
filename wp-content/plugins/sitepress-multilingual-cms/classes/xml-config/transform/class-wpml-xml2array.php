<?php

class WPML_XML2Array implements WPML_XML_Transform {
	private $contents;
	private $get_attributes;

	public function get( $contents, $get_attributes = true ) {
		$this->contents       = $contents;
		$this->get_attributes = (bool) $get_attributes;

		$xml_values = array();

		if ( $this->contents && function_exists( 'xml_parser_create' ) ) {
			$parser = xml_parser_create();
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parse_into_struct( $parser, $this->contents, $xml_values );
			xml_parser_free( $parser );
		}

		//Initializations
		$xml_array = array();

		$current = &$xml_array;

		//Go through the tags.
		foreach ( $xml_values as $data ) {
			unset( $attributes, $value );//Remove existing values, or there will be trouble

			$tag        = $data['tag'];
			$type       = $data['type'];
			$level      = $data['level'];
			$value      = isset( $data['value'] ) ? $data['value'] : '';
			$attributes = isset( $data['attributes'] ) ? $data['attributes'] : array();
			$item       = $this->get_item( $value, $attributes );

			//See tag status and do the needed.
			if ( 'open' === $type ) {//The starting of the tag '<tag>'
				$parent[ $level - 1 ] = &$current;

				if ( ! is_array( $current ) || ( ! isset( $current[ $tag ] ) ) ) { //Insert New tag
					$current[ $tag ] = $item;
					$current         = &$current[ $tag ];
				} else { //There was another element with the same tag name
					if ( isset( $current[ $tag ][0] ) ) {
						$current[ $tag ][] = $item;
					} else {
						$current[ $tag ] = array( $current[ $tag ], $item );
					}
					$last    = count( $current[ $tag ] ) - 1;
					$current = &$current[ $tag ][ $last ];
				}
			} elseif ( 'complete' === $type ) { //Tags that ends in 1 line '<tag />'
				//See if the key is already taken.
				if ( ! isset( $current[ $tag ] ) ) { //New Key
					$current[ $tag ] = $item;
				} else { //If taken, put all things inside a list(array)
					if ( ( is_array( $current[ $tag ] ) && ! $this->get_attributes )//If it is already an array...
					     || ( isset( $current[ $tag ][0] ) && is_array( $current[ $tag ][0] ) && $this->get_attributes )
					) {
						$current[ $tag ][] = $item;
					} else { //If it is not an array...
						$current[ $tag ] = array( $current[ $tag ], $item );
					}
				}
			} elseif ( 'close' === $type ) { //End of tag '</tag>'
				$current = &$parent[ $level - 1 ];
			}
		}

		return $xml_array;
	}

	/**
	 * @param       $value
	 * @param array $attributes
	 *
	 * @return array
	 */
	private function get_item( $value, array $attributes ) {
		$item = array();

		if ( $this->get_attributes ) {//The second argument of the function decides this.
			if ( null !== $value ) {
				$item['value'] = $value;
			}

			if ( null !== $attributes ) {
				foreach ( $attributes as $attr => $val ) {
					$item['attr'][ $attr ] = $val;
				}
			}
		} else {
			$item = $value;
		}

		return $item;
	}
}
