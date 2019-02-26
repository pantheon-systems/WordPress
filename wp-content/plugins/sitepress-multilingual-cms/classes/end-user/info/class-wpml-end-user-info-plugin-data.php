<?php

class WPML_End_User_Info_Plugin_Data implements WPML_End_User_Info {
	/** @var string */
	private $name;

	/** @var string */
	private $author;

	/** @var string */
	private $url;

	/**
	 * @param string $name
	 * @param string $author
	 * @param string $url
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $name, $author, $url ) {
		if ( empty( $name ) ) {
			throw new InvalidArgumentException( 'Plugin name cannot be emtpy' );
		}

		$this->name   = $name;
		$this->author = $author;
		$this->url    = $url;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return array(
			'name'   => $this->name,
			'author' => $this->author,
			'url'    => $this->url,
		);
	}
}
