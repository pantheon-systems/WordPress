<?php

class vcImageFilter {

	/**
	 * @var resource
	 */
	private $image;

	/**
	 * run constructor
	 *
	 * @param resource &$image GD image resource
	 */
	public function __construct( &$image ) {
		$this->image = $image;
	}

	/**
	 * Get the current image resource
	 *
	 * @return resource
	 */
	public function getImage() {
		return $this->image;
	}

	public function sepia() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 50, 0 );

		return $this;
	}

	public function sepia2() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 20 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 60, 30, - 15 );

		return $this;
	}

	public function sharpen() {
		$gaussian = array(
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, - 7.0, 1.0 ),
			array( 1.0, 1.0, 1.0 )
		);
		imageconvolution( $this->image, $gaussian, 1, 4 );

		return $this;
	}

	public function emboss() {
		$gaussian = array(
			array( - 2.0, - 1.0, 0.0 ),
			array( - 1.0, 1.0, 1.0 ),
			array( 0.0, 1.0, 2.0 )
		);

		imageconvolution( $this->image, $gaussian, 1, 5 );

		return $this;
	}

	public function cool() {
		imagefilter( $this->image, IMG_FILTER_MEAN_REMOVAL );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 50 );

		return $this;
	}

	public function light() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 50, 0, 10 );

		return $this;
	}

	public function aqua() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 70, 0, 30 );

		return $this;
	}

	public function fuzzy() {
		$gaussian = array(
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, 1.0, 1.0 ),
			array( 1.0, 1.0, 1.0 )
		);

		imageconvolution( $this->image, $gaussian, 9, 20 );

		return $this;
	}

	public function boost() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 35 );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );

		return $this;
	}

	public function gray() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 60 );
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );

		return $this;
	}

	public function antique() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 0 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 30 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 75, 50, 25 );

		return $this;
	}

	public function blackwhite() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 20 );

		return $this;
	}

	public function boost2() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 35 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 25, 25, 25 );

		return $this;
	}

	public function blur() {
		imagefilter( $this->image, IMG_FILTER_SELECTIVE_BLUR );
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 15 );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, - 2 );

		return $this;
	}

	public function vintage() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 40, 10, - 15 );

		return $this;
	}

	public function concentrate() {
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, - 10 );

		return $this;
	}

	public function hermajesty() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 5 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 80, 0, 60 );

		return $this;
	}

	public function everglow() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 30 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 5 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 30, 30, 0 );

		return $this;
	}

	public function freshblue() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, - 5 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 20, 0, 80, 60 );

		return $this;
	}

	public function tender() {
		imagefilter( $this->image, IMG_FILTER_CONTRAST, 5 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 80, 20, 40, 50 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 40, 40, 100 );
		imagefilter( $this->image, IMG_FILTER_SELECTIVE_BLUR );

		return $this;
	}

	public function dream() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 150, 0, 0, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 50, 0, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );

		return $this;
	}

	public function frozen() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 15 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 0, 100, 50 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 0, 100, 50 );
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );

		return $this;
	}

	public function forest() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 0, 150, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 0, 150, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, 10 );

		return $this;
	}

	public function rain() {
		imagefilter( $this->image, IMG_FILTER_GAUSSIAN_BLUR );
		imagefilter( $this->image, IMG_FILTER_MEAN_REMOVAL );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 80, 50, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, 10 );

		return $this;
	}

	public function orangepeel() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 20, - 50, 20 );
		imagefilter( $this->image, IMG_FILTER_SMOOTH, 10 );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 10 );
		imagefilter( $this->image, IMG_FILTER_CONTRAST, 10 );
		imagegammacorrect( $this->image, 1, 1.2 );

		return $this;
	}

	public function darken() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 50 );

		return $this;
	}

	public function summer() {
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 0, 150, 0, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 25, 50, 0, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );

		return $this;
	}

	public function retro() {
		imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 100, 25, 25, 50 );

		return $this;
	}

	public function country() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, - 30 );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, 50, 50, 50, 50 );
		imagegammacorrect( $this->image, 1, 0.3 );

		return $this;
	}

	public function washed() {
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 30 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_COLORIZE, - 50, 0, 20, 50 );
		imagefilter( $this->image, IMG_FILTER_NEGATE );
		imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, 10 );
		imagegammacorrect( $this->image, 1, 1.2 );

		return $this;
	}

}