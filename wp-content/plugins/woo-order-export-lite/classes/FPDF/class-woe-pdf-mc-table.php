<?php
if ( ! class_exists( 'FPDF' ) ) {
	require( 'fpdf.php' );
}

class WOE_PDF_MC_Table extends FPDF {
	protected $widths;
	protected $aligns;

	protected $table_header = array();

	protected $header_props = array();
	protected $footer_props = array();
	protected $table_header_props = array();
	protected $table_row_props = array();
	protected $table_props = array();

	protected $stretch_buffer = array();
	protected $stretch_buffer_params = array();

	protected $default_props = array(
		'header'       => array(
			'title'      => '',
			'style'      => 'B',
			'size'       => 5,
			'text_color' => array( 0, 0, 0 ),
			'logo' => array(
				'source'   => '',
				'width'    => 0,
				'height'   => 0,
				'align'    => 'R',
			),
		),
		'table'        => array(
			'stretch'      => false,
			'column_width' => array(),
		),
		'table_header' => array(
			'style'            => '',
			'size'             => 5,
			'text_color'       => array( 0, 0, 0 ),
			'background_color' => array( 255, 255, 255 ),
			'repeat'           => true,
		),
		'table_row'    => array(
			'style'            => '',
			'size'             => 5,
			'text_color'       => array( 0, 0, 0 ),
			'background_color' => array( 255, 255, 255 ),
		),
		'footer'       => array(
			'title'           => '',
			'style'           => 'B',
			'size'            => 5,
			'text_color'      => array( 0, 0, 0 ),
			'pagination_type' => '',
		),
	);

	public function setProperties( $props ) {
		foreach ( $this->default_props as $key => $default_props ) {
			if ( ! empty( $props[ $key ] ) && is_array( $props[ $key ] ) ) {

				$name = $key . '_props';
				if ( ! isset( $this->$name ) ) {
					continue;
				}

				$this->$name = array_merge( $default_props, $props[ $key ] );
			}
		}
	}

	public function setHeaderProperty( $props ) {
		$this->header_props = array_merge( $this->default_props['header'], $props );
	}

	public function addTableHeader( $header ) {
		$this->table_header = $header;
		$this->changeBrushToDraw( 'table_header' );
		$this->Row( $header );
	}

	public function setTableHeaderProperty( $props ) {
		$this->table_header_props = array_merge( $this->default_props['table_header'], $props );
	}

	public function setTableRowProperty( $props ) {
		$this->table_row_props = array_merge( $this->default_props['table_header'], $props );
	}

	public function setFooterProperty( $props ) {
		$this->footer_props = array_merge( $this->default_props['footer'], $props );
	}

	public function Header() {
		if ( ! empty( $this->header_props['title'] ) ) {
			$this->changeBrushToDraw( 'header' );
			$this->Cell( 0, 0, $this->header_props['title'], 0, 0, 'C' );
			$this->Ln( 2 );
		}

		if ( $this->drawLogo() ) {
			$this->Ln( 1 );
		}
	}

	protected function drawLogo() {
		$source = $this->header_props['logo']['source'];
		$width  = $this->header_props['logo']['width'];
		$height = $this->header_props['logo']['height'];
		$align  = $this->header_props['logo']['align'];

		if ( ! $source || ! $height ) {
			return false;
		}

		$height = $this->validateHeight( $height );
		if ( ! $width ) {
			list( $img_width, $img_height, $type, $attr ) = getimagesize( $source );
			$width = $height * $img_width / $img_height;
		}
		$width = $this->validateWidth( $width );

		if ( $align == 'R' ) {
			$x = $this->GetPageWidth() - $this->rMargin - $width;
		} elseif ( $align == 'C' ) {
			$x = ( $this->GetPageWidth() - $width ) / 2;
		} else {
			$x = $this->lMargin;
		}

		$type = strtoupper( pathinfo( $source, PATHINFO_EXTENSION ) );

		$this->Image( $source, $x, $this->GetY(), $width, $height, $type );
		$this->Ln( $height );

		return true;
	}

	public function Footer() {
		$this->SetY( - 15 );

		$this->changeBrushToDraw( 'footer' );

		if ( ! empty( $this->footer_props['title'] ) ) {
			// Title
			$this->Cell( 0, 0, $this->footer_props['title'], 0, 0, 'C' );
			// Line break
			$this->Ln( 10 );
		}

		// Position at 1.5 cm from bottom
		$this->SetY( - 15 );

		if ( ! empty( $this->footer_props['pagination'] ) ) {
			// Page number
			$align = in_array( $this->footer_props['pagination'], array( 'L', 'C', 'R', ) ) ? $this->footer_props['pagination'] : false;
			if ( $align ) {
				$this->Cell( 0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, $align );
			}
		}
	}

	public function addRow( $data, $widths = null, $h = null ) {
		$this->changeBrushToDraw( 'table_row' );
		$this->Row( $data, $widths, $h );
	}

	protected function Row( $data, $widths = null, $h = null ) {
		if ( ! $data ) {
			return;
		}

		$widths = ! $widths ? $this->getRowWidths( $data ) : $widths;
		$h      = ! $h ? $this->getRowHeight( $widths, $data ) : $h;

		//Issue a page break first if needed
		$this->CheckPageBreak( $h );

		$columns_count = $this->getColumnCountInPage( $widths );
		if ( $extra_data = array_slice( $data, $columns_count ) ) {
			$this->stretch_buffer[]        = $extra_data;
			$this->stretch_buffer_params[] = array(
				'widths' => array_slice( $widths, $columns_count ),
				'height' => $h,
			);
		}
		$data = array_slice( $data, 0, $columns_count );


		//Draw the cells of the row
		for ( $i = 0; $i < count( $data ); $i ++ ) {
			$w = $widths[ $i ];
			$a = isset( $this->aligns[ $i ] ) ? $this->aligns[ $i ] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			$this->Rect( $x, $y, $w, $h, 'DF' );
			//Print the text
			$this->MultiCell( $w, 5, $data[ $i ], 0, $a );
			//Put the position to the right of the cell
			$this->SetXY( $x + $w, $y );
		}
		//Go to the next line
		$this->Ln( $h );
	}

	protected function getColumnCountInPage( $widths ) {
		$count = count( $widths );
		if ( $this->table_props['stretch'] ) {
			$sum_width  = 0;
			$page_width = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
			$count      = 0;
			foreach ( $widths as $width ) {
				if ( $sum_width + $width > $page_width ) {
					break;
				}
				$sum_width += $width;
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Calculate the width for every column of the row
	 *
	 * @param $row
	 *
	 * @return array
	 */
	protected function getRowWidths( $row ) {
		if ( $this->table_props['stretch'] ) {
			$widths = array();
			for ( $i = 0; $i < count( $row ); $i ++ ) {
				$width = isset( $this->table_props['column_width'][ $i ] ) ? $this->table_props['column_width'][ $i ] :
					$this->table_props['column_width'][ $i % count( $this->table_props['column_width'] ) ];

				$widths[ $i ] = $this->validateWidth( $width );
			}

			return $widths;
		} else {
			return array_fill( 0, count( $row ), ( $this->GetPageWidth() - $this->lMargin - $this->rMargin ) / count( $row ) );
		}
	}

	protected function validateWidth( $width, $min_width = 5 ) {
		$max_width = $this->GetPageWidth() - $this->lMargin - $this->rMargin - 50;
		if ( $width < $min_width ) {
			$width = $min_width;
		} elseif ( $width > $max_width ) {
			$width = $max_width;
		}

		return $width;
	}

	protected function validateHeight( $height, $min_height = 5 ) {
		$max_height = $this->GetPageHeight() - $this->tMargin - $this->bMargin - 50;
		if ( $height < $min_height ) {
			$height = $min_height;
		} elseif ( $height > $max_height ) {
			$height = $max_height;
		}

		return $height;
	}

	/**
	 * Calculate the height of the row
	 *
	 * @param $widths
	 * @param $row
	 *
	 * @return int|mixed
	 */
	protected function getRowHeight( $widths, $row ) {
		$nb = 0;
		for ( $i = 0; $i < count( $row ); $i ++ ) {
			$nb = max( $nb, $this->NbLines( $widths[ $i ], $row[ $i ] ) );
		}

		return 5 * $nb;
	}

	public function CheckPageBreak( $h ) {
		//If the height h would cause an overflow, add a new page immediately
		if ( $this->GetY() + $h > $this->PageBreakTrigger ) {
			$this->flush_buffer();

			$this->AddPage( $this->CurOrientation );
			if ( $this->table_header_props['repeat'] && $this->table_header ) {
				$this->changeBrushToDraw( 'table_header' );
				$this->Row( $this->table_header );
				$this->changeBrushToDraw( 'table_row' );
			}
		}
	}

	public function Output( $dest = '', $name = '', $isUTF8 = false ) {
		$this->flush_buffer();
		parent::Output( $dest, $name, $isUTF8 );
	}

	protected function flush_buffer() {
		while ( $this->stretch_buffer ) {
			$this->AddPage( $this->CurOrientation );

			$buffer                      = $this->stretch_buffer;
			$stretch_buffer_params       = $this->stretch_buffer_params;
			$this->stretch_buffer        = array();
			$this->stretch_buffer_params = array();

			if ( $this->table_header ) {
				$this->changeBrushToDraw( 'table_header' );
				$params = array_shift( $stretch_buffer_params );
				$this->Row( array_shift( $buffer ), $params['widths'], $params['height'] );
				$this->changeBrushToDraw( 'table_row' );
			}

			foreach ( $buffer as $index => $row ) {
				$params = $stretch_buffer_params[ $index ];
				$this->addRow( $row, $params['widths'], $params['height'] );
			}
		}
	}

	public function NbLines( $w, $txt ) {
		//Computes the number of lines a MultiCell of width w will take
		$cw =& $this->CurrentFont['cw'];
		if ( $w == 0 ) {
			$w = $this->w - $this->rMargin - $this->x;
		}
		$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->FontSize;
		$s    = str_replace( "\r", '', $txt );
		$nb   = strlen( $s );
		if ( $nb > 0 and $s[ $nb - 1 ] == "\n" ) {
			$nb --;
		}
		$sep = - 1;
		$i   = 0;
		$j   = 0;
		$l   = 0;
		$nl  = 1;
		while ( $i < $nb ) {
			$c = $s[ $i ];
			if ( $c == "\n" ) {
				$i ++;
				$sep = - 1;
				$j   = $i;
				$l   = 0;
				$nl ++;
				continue;
			}
			if ( $c == ' ' ) {
				$sep = $i;
			}
			$l += $cw[ $c ];
			if ( $l > $wmax ) {
				if ( $sep == - 1 ) {
					if ( $i == $j ) {
						$i ++;
					}
				} else {
					$i = $sep + 1;
				}
				$sep = - 1;
				$j   = $i;
				$l   = 0;
				$nl ++;
			} else {
				$i ++;
			}
		}

		return $nl;
	}

	public function SetAligns( $a ) {
		//Set the array of column alignments
		$this->aligns = $a;
	}

	protected function changeBrushToDraw( $what ) {
		if ( ! in_array( $what, array_keys( $this->default_props ) ) ) {
			return false;
		}

		$name = $what . '_props';
		if ( ! isset( $this->$name ) ) {
			return false;
		}
		$props = $this->$name;

		$this->SetFont( $this->FontFamily, $props['style'], $props['size'] );
		$this->loadTextColor( $props );
		$this->loadFillColor( $props );

		return true;
	}

	private function loadTextColor( $props ) {
		$color = ! empty( $props['text_color'] ) ? $props['text_color'] : array();

		if ( $color ) {
			$color = $this->convert_color( $color );
			$this->SetTextColor( $color[0], $color[1], $color[2] );
		}
	}

	private function loadFillColor( $props ) {
		$color = ! empty( $props['background_color'] ) ? $props['background_color'] : array();

		if ( $color ) {
			$color = $this->convert_color( $color );
			$this->SetFillColor( $color[0], $color[1], $color[2] );
		}
	}

	private function convert_color( $array ) {
		for ( $i = 0; $i < 2; $i ++ ) {
			$array[ $i ] = ( isset( $array[ $i ] ) && is_numeric( $array[ $i ] ) && $array[ $i ] >= 0 && $array[ $i ] <= 255 ) ? $array[ $i ] : null;
		}

		return array_slice( $array, 0, 3 );
	}

}

