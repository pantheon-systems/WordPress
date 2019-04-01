<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once 'abstract-class-woe-formatter-plain-format.php';
include_once 'class-woe-formatter-csv.php';

if ( ! class_exists( 'WOE_PDF_MC_Table' ) ) {
	include_once dirname( __FILE__ ) . '/../FPDF/class-woe-pdf-mc-table.php';
}

/**
 * Class WOE_Formatter_PDF
 *
 * Using CSV formatter as basis. Works like CSV (even creates csv file) but after finish,
 * fetches data from file and paste them to PDF as table
 */
class WOE_Formatter_PDF extends WOE_Formatter_Csv {
	/** @var $pdf WOE_PDF_MC_Table */
	protected $pdf;

	private $orientation = 'P';
	private $page_size = 'A4';
	private $font_size = 5;
	private $repeat_header = false;

	public function __construct( $mode, $filename, $settings, $format, $labels, $field_formats, $date_format, $offset ) {

		$settings['enclosure'] = '"';
		$settings['linebreak'] = '\r\n';
		$settings['delimiter'] = ',';
		$settings['encoding']  = 'UTF-8';

		$this->orientation   = ! empty( $settings['orientation'] ) ? $settings['orientation'] : 'P';
		$this->page_size     = ! empty( $settings['page_size'] ) ? $settings['page_size'] : 'A4';
		$this->font_size     = ! empty( $settings['font_size'] ) ? $settings['font_size'] : 5;
		$this->repeat_header = ! empty( $settings['repeat_header'] );

		if ( $mode != 'preview' ) {
			$filename = str_replace( '.pdf', '.csv', $filename );
		}

		parent::__construct( $mode, $filename, $settings, $format, $labels, $field_formats, $date_format, $offset );
	}

	public function finish() {
		parent::finish();

		if ( $this->mode != 'preview' ) {
			$this->pdf = new WOE_PDF_MC_Table( $this->orientation, 'mm', $this->page_size );

			$this->pdf->SetFont( 'Arial', '', $this->font_size );
			$this->pdf->SetFillColor( null );
			$this->pdf->setProperties( array(
				'header'       => array(
					'title'      => $this->settings['header_text'],
					'style'      => 'B',
					'size'       => '10',
					'text_color' => $this->hex2RGB( $this->settings['page_header_text_color'] ),
					'logo' => array(
						'source'   => $this->settings['logo_source'],
						'width'    => $this->settings['logo_width'],
						'height'   => $this->settings['logo_height'],
						'align'    => $this->settings['logo_align'],
					),
				),
				'table'        => array(
					'stretch'      => !$this->settings['fit_page_width'],
					'column_width' => explode( ",", $this->settings['cols_width'] ),
				),
				'table_header' => array(
					'size'             => 10,
					'repeat'           => $this->repeat_header,
					'text_color'       => $this->hex2RGB( $this->settings['table_header_text_color'] ),
					'background_color' => $this->hex2RGB( $this->settings['table_header_background_color'] ),
				),
				'table_row'    => array(
					'size'             => $this->font_size,
					'text_color'       => $this->hex2RGB( $this->settings['table_row_text_color'] ),
					'background_color' => $this->hex2RGB( $this->settings['table_row_background_color'] ),
				),
				'footer'       => array(
					'title'      => $this->settings['footer_text'],
					'style'      => 'B',
					'size'       => '10',
					'text_color' => $this->hex2RGB( $this->settings['page_footer_text_color'] ),
					'pagination' => $this->settings['pagination'],
				),
			) );
			$this->pdf->SetAligns( explode( ",", $this->settings['cols_align'] ) );
			do_action("woe_pdf_started", $this->pdf, $this);

			$this->pdf->AliasNbPages();
			$this->pdf->AddPage();

			$this->handle = fopen( $this->filename, 'r' );
			$row          = fgetcsv( $this->handle, 0, $this->delimiter, $this->enclosure );

			if ( ! empty( $this->settings['display_column_names'] ) and $row ) {
				$this->pdf->addTableHeader( $row );
				do_action("woe_pdf_below_header", $this->pdf, $this);
				$row = fgetcsv( $this->handle, 0, $this->delimiter, $this->enclosure );
			}

			while ( $row ) {
				$this->pdf->addRow( $row );
				$row = fgetcsv( $this->handle, 0, $this->delimiter, $this->enclosure );
			}
			
			do_action("woe_pdf_finished", $this->pdf, $this);
			$this->pdf->Output( 'f', str_replace( '.csv', '.pdf', $this->filename ) );
		}
	}

	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param string $hexStr (hexadecimal color value)
	 * @return array|boolean Returns False if invalid hex color value
	 */
	private function hex2RGB( $hexStr ) {
		$hexStr   = preg_replace( "/[^0-9A-Fa-f]/", '', $hexStr ); // Gets a proper hex string
		$rgbArray = array();
		if ( strlen( $hexStr ) == 6 ) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal    = hexdec( $hexStr );
			$rgbArray[0] = 0xFF & ( $colorVal >> 0x10 );
			$rgbArray[1] = 0xFF & ( $colorVal >> 0x8 );
			$rgbArray[2] = 0xFF & $colorVal;
		} elseif ( strlen( $hexStr ) == 3 ) { //if shorthand notation, need some string manipulations
			$rgbArray[0] = hexdec( str_repeat( substr( $hexStr, 0, 1 ), 2 ) );
			$rgbArray[1] = hexdec( str_repeat( substr( $hexStr, 1, 1 ), 2 ) );
			$rgbArray[2] = hexdec( str_repeat( substr( $hexStr, 2, 1 ), 2 ) );
		} else {
			return false; //Invalid hex color code
		}

		return $rgbArray; // returns the rgb string or the associative array
	}


}