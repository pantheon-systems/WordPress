<?php

class WPML_TM_Count implements IWPML_TM_Count {

	/** @var int $total */
	private $total = 0;

	/** @var array $to_translate */
	private $to_translate;

	/**
	 * @param string|null $json_data
	 */
	public function __construct( $json_data = null ) {
		if ( $json_data ) {
			$this->set_properties_from_json( $json_data );
		}
	}

	/** @param string $json_data */
	public function set_properties_from_json( $json_data ) {
		$data = json_decode( $json_data, true );

		if ( isset( $data['total'] ) ) {
			$this->total = (int) $data['total'];
		}

		if ( isset( $data['to_translate'] ) ) {
			$this->to_translate = $data['to_translate'];
		}
	}

	/** @return int */
	public function get_total_words() {
		return $this->total;
	}

	/** @param int $total */
	public function set_total_words( $total ) {
		$this->total = $total;
	}

	/**
	 * @param string $lang
	 *
	 * @return int|null
	 */
	public function get_words_to_translate( $lang ) {
		if ( isset( $this->to_translate[ $lang ] ) ) {
			return (int) $this->to_translate[ $lang ];
		}

		return null;
	}

	/** @return string */
	public function to_string() {
		return json_encode(
			array(
				'total'        => $this->total,
				'to_translate' => $this->to_translate,
			)
		);
	}

	public function set_words_to_translate( $lang, $quantity ) {
		$this->to_translate[ $lang ] = $quantity;
	}
}
