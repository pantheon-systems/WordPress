<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_XLIFF {
	/** @var DOMElement */
	private $body;
	/** @var DOMDocument */
	private $dom;
	/** @var DOMDocumentType */
	private $dtd;
	/** @var DOMElement */
	private $file;
	/** @var DOMElement */
	private $file_header;
	/** @var DOMElement */
	private $file_reference;
	/** @var DOMElement */
	private $phase_group;
	/** @var DOMElement */
	private $root;
	/** @var array */
	private $trans_units;
	/** @var string */
	private $xliff_version;

	/**
	 * WPML_TM_XLIFF constructor.
	 *
	 * @param string $xliff_version
	 * @param string $xml_version
	 * @param string $xml_encoding
	 */
	public function __construct( $xliff_version = '1.2', $xml_version = '1.0', $xml_encoding = 'utf-8' ) {
		$this->dom = new DOMDocument( $xml_version, $xml_encoding );

		$this->root        = $this->dom->createElement( 'xliff' );
		$this->file        = $this->dom->createElement( 'file' );
		$this->file_header = $this->dom->createElement( 'header' );
		$this->body        = $this->dom->createElement( 'body' );

		$this->xliff_version = $xliff_version;
	}

	/**
	 * @param array $attributes
	 *
	 * @return $this
	 */
	public function setFileAttributes( $attributes ) {
		foreach ( $attributes as $name => $value ) {
			$this->file->setAttribute( $name, $value );
		}

		return $this;
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	public function setPhaseGroup( array $args ) {
		if ( $args ) {
			$phase_items = array();
			foreach ( $args as $name => $data ) {
				if ( $name
				     && array_key_exists( 'process-name', $data )
				     && array_key_exists( 'note', $data )
				     && $data['note'] ) {

					$phase = $this->dom->createElement( 'phase' );
					$phase->setAttribute( 'phase-name', $name );
					$phase->setAttribute( 'process-name', $data['process-name'] );
					$note            = $this->dom->createElement( 'note' );
					$note->nodeValue = $data['note'];
					$phase->appendChild( $note );
					$phase_items[] = $phase;

				}
			}

			if ( $phase_items ) {
				$this->phase_group = $this->dom->createElement( 'phase-group' );
				foreach ( $phase_items as $phase_item ) {
					$this->phase_group->appendChild( $phase_item );
				}
			}
		}

		return $this;
	}

	/**
	 * @param array $references
	 *
	 * @return $this
	 */
	public function setReferences( array $references ) {
		if ( $references ) {
			foreach ( $references as $name => $value ) {
				if ( ! $value ) {
					continue;
				}

				if ( ! $this->file_reference ) {
					$this->file_reference = $this->dom->createElement( 'reference' );
				}

				$reference = $this->dom->createElement( $name );
				$reference->setAttribute( 'href', $value );
				$this->file_reference->appendChild( $reference );
			}
		}

		return $this;
	}

	/**
	 * @param array $trans_units
	 *
	 * @return $this
	 */
	public function setTranslationUnits( $trans_units ) {
		if ( $trans_units ) {
			foreach ( $trans_units as $trans_unit ) {
				$trans_unit_element = $this->dom->createElement( 'trans-unit' );
				if ( array_key_exists( 'attributes', $trans_unit ) && is_array( $trans_unit['attributes'] ) ) {
					foreach ( $trans_unit['attributes'] as $name => $value ) {
						$trans_unit_element->setAttribute( $name, $value );
					}
				}
				$this->appendData( 'source', $trans_unit, $trans_unit_element );
				$this->appendData( 'target', $trans_unit, $trans_unit_element );

				$this->trans_units[] = $trans_unit_element;
			}
		}

		return $this;
	}

	/**
	 * @param string     $type
	 * @param array      $trans_unit
	 * @param DOMElement $trans_unit_element
	 */
	private function appendData( $type, $trans_unit, $trans_unit_element ) {
		if ( array_key_exists( $type, $trans_unit ) ) {
			$source       = $this->dom->createElement( $type );
			$datatype = isset( $trans_unit['attributes']['datatype'] ) ? $trans_unit['attributes']['datatype'] : '';
			$source_cdata = $this->dom->createCDATASection(
				$this->validate( $datatype, $trans_unit[ $type ]['content'] )
			);
			$source->appendChild( $source_cdata );

			if ( array_key_exists( 'attributes', $trans_unit[ $type ] ) ) {

				foreach ( $trans_unit[ $type ]['attributes'] as $name => $value ) {
					$source->setAttribute( $name, $value );
				}
			}

			$trans_unit_element->appendChild( $source );
		}
	}

	/**
	 * Validate content.
	 *
	 * @param string $datatype Type of content data.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	private function validate( $datatype, $content ) {
		if ( 'html' === $datatype ) {
			$validator = new WPML_TM_Validate_HTML();

			$validator->validate( $content );
			return $validator->get_html();
		}

		return $content;
	}

	public function toString() {
		$this->compose();

		return trim( $this->dom->saveXML() );
	}

	private function compose() {
		$this->dom->xmlStandalone = false;

		$this->setRoot( $this->xliff_version );

		if ( $this->dtd ) {
			$this->dom->appendChild( $this->dtd );
		}

		if ( $this->phase_group ) {
			$this->file_header->appendChild( $this->phase_group );
		}
		if ( $this->file_reference ) {
			$this->file_header->appendChild( $this->file_reference );
		}

		if ( $this->trans_units ) {
			foreach ( $this->trans_units as $trans_unit ) {
				$this->body->appendChild( $trans_unit );
			}
		}

		$this->file->appendChild( $this->file_header );
		$this->file->appendChild( $this->body );
		$this->root->appendChild( $this->file );
		$this->dom->appendChild( $this->root );
	}

	private function setRoot( $version ) {
		if ( $version === '1.0' ) {
			$implementation = new DOMImplementation();

			$this->dtd = $implementation->createDocumentType( 'xliff',
			                                                  '-//XLIFF//DTD XLIFF//EN',
			                                                  'http://www.oasis-open.org/committees/xliff/documents/xliff.dtd' );
		}
		$this->root->setAttribute( 'version', $version );
		$this->root->setAttribute( 'xmlns', 'urn:oasis:names:tc:xliff:document:' . $version );
	}

}