<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * @var WC_Order_Export_Admin $WC_Order_Export WC_Order_Export_Admin instance
 * @var string                $mode            ( now | profiles | cron | order-action )
 * @var integer               $id              job id
 * @var string                $ajaxurl
 * @var array                 $show
 *
 */
$settings                 = WC_Order_Export_Manage::get( $mode, $id );
$settings                 = apply_filters( 'woe_settings_page_prepare', $settings );
$order_custom_meta_fields = WC_Order_Export_Data_Extractor_UI::get_all_order_custom_meta_fields();
$readonly_php             = WC_Order_Export_Admin::user_can_add_custom_php() ? '' : 'readonly';

$pdf_format_available_options = array(
	'orientation' => array(
		'P' => 'Portrait',
		'L' => 'Landscape',
	),
	'page_size'   => array(
		'A3'     => 'A3',
		'A4'     => 'A4',
		'A5'     => 'A5',
		'letter' => 'Letter',
		'legal'  => 'Legal',
	),
);

function print_formats_field( $type, $segment = "" ) {
	if ( ! $type && $type !== 'meta' && $type !== 'field' ) {
		return '';
	}
	$margin_left = 'meta' == $type ? '1px' : '4px';
	// colname_custom_field
	$id = $segment ? 'format_custom_' . $type . '_' . $segment : 'format_custom_' . $type;

	$formats_fields_html =
		'<label for="' . $id . '">' .
		__( 'Field format', 'woo-order-export-lite' ) . ':' .
		'</label>' .
		'<select type="text" id="' . $id . '" style="max-width: 215px; margin-left: ' . $margin_left . '">' .
		'<option value="" >' . __( '-', 'woo-order-export-lite' ) . '</option>';

	foreach ( WC_Order_Export_Data_Extractor_UI::get_format_fields() as $format_id => $format_label ) {
		$formats_fields_html .= "<option value='$format_id' >$format_label</option>";
	};
	$formats_fields_html .= '</select>';

	return $formats_fields_html;
}


?>

<script>
	var mode = '<?php echo $mode ?>';
	var job_id = '<?php echo esc_js($id) ?>';
	var output_format = '<?php echo $settings['format'] ?>';
	var selected_order_fields = <?php echo json_encode( $settings['order_fields'] ) ?>;
	var selected_order_products_fields = <?php echo json_encode( $settings['order_product_fields'] ) ?>;
	var selected_order_coupons_fields = <?php echo json_encode( $settings['order_coupon_fields'] ) ?>;
	var duplicated_fields_settings = <?php echo json_encode( $settings['duplicated_fields_settings'] ) ?>;
	var all_fields = <?php echo json_encode( WC_Order_Export_Manage::make_all_fields( $settings['format'] ) ); ?>;
	var order_custom_meta_fields = <?php echo json_encode( $order_custom_meta_fields ) ?>;
	var order_products_custom_meta_fields = <?php echo json_encode( WC_Order_Export_Data_Extractor_UI::get_product_custom_fields() ) ?>;
	var order_order_item_custom_meta_fields = <?php echo json_encode( WC_Order_Export_Data_Extractor_UI::get_product_itemmeta() ) ?>;
	var order_coupons_custom_meta_fields = <?php echo json_encode( WC_Order_Export_Data_Extractor_UI::get_all_coupon_custom_meta_fields() ) ?>;
	var order_segments = <?php echo json_encode( WC_Order_Export_Data_Extractor_UI::get_order_segments() ) ?>;
	var field_formats = <?php echo json_encode( WC_Order_Export_Data_Extractor_UI::get_format_fields() ) ?>;
	var summary_mode = <?php echo $settings['summary_report_by_products'] ?>;

	jQuery( document ).ready( function ( $ ) {
		$( 'input.color_pick' ).wpColorPicker();
	} );
</script>


<form method="post" id="export_job_settings">
	<?php if ( $mode !== WC_Order_Export_Manage::EXPORT_NOW ): ?>
        <div style="width: 100%;">&nbsp;</div>
	<?php endif; ?>

    <div id="my-left" style="float: left; width: 49%; max-width: 500px;">
		<?php
		if ( $mode === WC_Order_Export_Manage::EXPORT_PROFILE ):
			include 'pro-version/top-profile.php';
        elseif ( $mode === WC_Order_Export_Manage::EXPORT_ORDER_ACTION ):
			include 'pro-version/top-order-actions.php';
        elseif ( $mode === WC_Order_Export_Manage::EXPORT_SCHEDULE ):
			include 'pro-version/top-scheduled-jobs.php';
		endif;
		?>

        <input type="hidden" name="settings[version]"
               value="<?php echo isset( $settings['version'] ) ? $settings['version'] : '2.0' ?>">

		<?php if ( $show['date_filter'] ) : ?>
            <div id="my-export-date-field" class="my-block">
                <div class="wc-oe-header">
					<?php _e( 'Filter orders by', 'woo-order-export-lite' ) ?>:
                </div>
                <label>
                    <input type="radio" name="settings[export_rule_field]"
                           class="width-100" <?php echo ( ! isset( $settings['export_rule_field'] ) || ( $settings['export_rule_field'] == 'date' ) ) ? 'checked' : '' ?>
                           value="date">
					<?php _e( 'Order Date', 'woo-order-export-lite' ) ?>
                </label>
                &#09;&#09;
                <label>
                    <input type="radio" name="settings[export_rule_field]"
                           class="width-100" <?php echo ( isset( $settings['export_rule_field'] ) && ( $settings['export_rule_field'] == 'modified' ) ) ? 'checked' : '' ?>
                           value="modified">
					<?php _e( 'Modification Date', 'woo-order-export-lite' ) ?>
                </label>
                &#09;&#09;
                <label>
                    <input type="radio" name="settings[export_rule_field]"
                           class="width-100" <?php echo ( isset( $settings['export_rule_field'] ) && ( $settings['export_rule_field'] == 'date_paid' ) ) ? 'checked' : '' ?>
                           value="date_paid">
					<?php _e( 'Paid Date', 'woo-order-export-lite' ) ?>
                </label>
                &#09;&#09;
                <label>
                    <input type="radio" name="settings[export_rule_field]"
                           class="width-100" <?php echo ( isset( $settings['export_rule_field'] ) && ( $settings['export_rule_field'] == 'date_completed' ) ) ? 'checked' : '' ?>
                           value="date_completed">
					<?php _e( 'Completed Date', 'woo-order-export-lite' ) ?>
                </label>
            </div>
            <br>
            <div id="my-date-filter" class="my-block"
                 title="<?php _e( 'This date range should not be saved in the scheduled task',
				     'woo-order-export-lite' ) ?>">
                <div style="display: inline;">
                    <span class="wc-oe-header"><?php _e( 'Date range', 'woo-order-export-lite' ) ?></span>
                    <input type=text class='date' name="settings[from_date]" id="from_date"
                           value='<?php echo $settings['from_date'] ?>'>
					<?php _e( 'to', 'woo-order-export-lite' ) ?>
                    <input type=text class='date' name="settings[to_date]" id="to_date"
                           value='<?php echo $settings['to_date'] ?>'>
                </div>

                <button id="my-quick-export-btn" class="button-primary"><?php _e( 'Express export',
						'woo-order-export-lite' ) ?></button>
                <div id="summary_report_by_products" style="display:inline-block"><input type="hidden"
                                                                                         name="settings[summary_report_by_products]"
                                                                                         value="0"/><label><input
                                type="checkbox" id=summary_report_by_products_checkbox
                                name="settings[summary_report_by_products]"
                                value="1" <?php checked( $settings['summary_report_by_products'] ) ?> /> <?php _e( "Summary Report By Products",
							'woo-order-export-lite' ) ?></label>
                </div>
            </div>
            <br>
		<?php endif; ?>

        <div id="my-export-file" class="my-block">
            <div class="wc-oe-header">
				<?php _e( 'Export filename', 'woo-order-export-lite' ) ?>:
            </div>
            <label id="export_filename" class="width-100">
                <input type="text" name="settings[export_filename]" class="width-100"
                       value="<?php echo isset( $settings['export_filename'] ) ? $settings['export_filename'] : 'orders-%y-%m-%d-%h-%i-%s.xlsx' ?>">
            </label>
        </div>
        <br>


        <div id="my-format" class="my-block">
            <span class="wc-oe-header"><?php _e( 'Format', 'woo-order-export-lite' ) ?></span><br>
            <p>
				<?php foreach ( WC_Order_Export_Admin::$formats as $format ) { ?>
                    <label class="button-secondary">
                        <input type=radio name="settings[format]" class="output_format" value="<?php echo $format ?>"
							<?php if ( $format == $settings['format'] ) {
								echo 'checked';
							} ?> ><?php echo $format ?>
                        <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
                    </label>
				<?php } ?>
            </p>

            <div id='XLS_options' style='display:none'><strong><?php _e( 'XLS options',
						'woo-order-export-lite' ) ?></strong><br>
                <input type=hidden name="settings[format_xls_use_xls_format]" value=0>
                <input type=hidden name="settings[format_xls_display_column_names]" value=0>
                <input type=hidden name="settings[format_xls_auto_width]" value=0>
                <input type=hidden name="settings[format_xls_direction_rtl]" value=0>
                <input type=hidden name="settings[format_xls_force_general_format]" value=0>
                <input type=checkbox name="settings[format_xls_use_xls_format]"
                       value=1 <?php if ( @$settings['format_xls_use_xls_format'] ) {
					echo 'checked';
				} ?> id="format_xls_use_xls_format"> <?php _e( 'Export as .xls (Binary File Format)',
					'woo-order-export-lite' ) ?><br>
                <input type=checkbox checked disabled><?php _e( 'Use sheet name', 'woo-order-export-lite' ) ?></b>
                <input type=text name="settings[format_xls_sheet_name]"
                       value='<?php echo $settings['format_xls_sheet_name'] ?>' size=10><br>
                <input type=checkbox name="settings[format_xls_display_column_names]"
                       value=1 <?php if ( @$settings['format_xls_display_column_names'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Output column titles as first line', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_xls_auto_width]"
                       value=1 <?php if ( @$settings['format_xls_auto_width'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Auto column width', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_xls_direction_rtl]"
                       value=1 <?php if ( @$settings['format_xls_direction_rtl'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Right-to-Left direction', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_xls_force_general_format]"
                       value=1 <?php if ( @$settings['format_xls_force_general_format'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Force general format for all cells', 'woo-order-export-lite' ) ?><br>
            </div>
            <div id='CSV_options' style='display:none'><strong><?php _e( 'CSV options',
						'woo-order-export-lite' ) ?></strong><br>
                <input type=hidden name="settings[format_csv_add_utf8_bom]" value=0>
                <input type=hidden name="settings[format_csv_display_column_names]" value=0>
                <input type=hidden name="settings[format_csv_delete_linebreaks]" value=0>
                <input type=hidden name="settings[format_csv_item_rows_start_from_new_line]" value=0>
                <input type=checkbox name="settings[format_csv_add_utf8_bom]"
                       value=1 <?php if ( @$settings['format_csv_add_utf8_bom'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Output UTF-8 BOM', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_csv_display_column_names]"
                       value=1 <?php if ( @$settings['format_csv_display_column_names'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Output column titles as first line', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_csv_delete_linebreaks]"
                       value=1 <?php if ( @$settings['format_csv_delete_linebreaks'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Convert line breaks to literals', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_csv_item_rows_start_from_new_line]"
                       value=1 <?php if ( @$settings['format_csv_item_rows_start_from_new_line'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Item rows start from new line', 'woo-order-export-lite' ) ?><br>
				<?php _e( 'Enclosure', 'woo-order-export-lite' ) ?> <input type=text
                                                                              name="settings[format_csv_enclosure]"
                                                                              value='<?php echo $settings['format_csv_enclosure'] ?>'
                                                                              size=1>
				<?php _e( 'Field Delimiter', 'woo-order-export-lite' ) ?> <input type=text
                                                                                    name="settings[format_csv_delimiter]"
                                                                                    value='<?php echo $settings['format_csv_delimiter'] ?>'
                                                                                    size=1>
				<?php _e( 'Line Break', 'woo-order-export-lite' ) ?><input type=text
                                                                              name="settings[format_csv_linebreak]"
                                                                              value='<?php echo $settings['format_csv_linebreak'] ?>'
                                                                              size=4><br>
				<?php if ( function_exists( 'iconv' ) ): ?>
					<?php _e( 'Character encoding', 'woo-order-export-lite' ) ?><input type=text
                                                                                          name="settings[format_csv_encoding]"
                                                                                          value="<?php echo $settings['format_csv_encoding'] ?>">
                    <br>
				<?php endif ?>
            </div>
            <div id='XML_options' style='display:none'><strong><?php _e( 'XML options',
						'woo-order-export-lite' ) ?></strong><br>
				<?php if( !class_exists("XMLWriter") ): ?>
					<div style="color:red"><?php _e( 'Please, install/enable PHP XML Extension!','woo-order-export-lite' ) ?></div>
				<?php endif ?>
                <input type=hidden name="settings[format_xml_self_closing_tags]" value=0>
                <span class="xml-title"><?php _e( 'Prepend XML', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                              name="settings[format_xml_prepend_raw_xml]"
                                                                                                              value='<?php echo $settings['format_xml_prepend_raw_xml'] ?>'><br>
                <span class="xml-title"><?php _e( 'Root tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                           name="settings[format_xml_root_tag]"
                                                                                                           value='<?php echo $settings['format_xml_root_tag'] ?>'><br>
                <span class="xml-title"><?php _e( 'Order tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                            name="settings[format_xml_order_tag]"
                                                                                                            value='<?php echo $settings['format_xml_order_tag'] ?>'><br>
                <span class="xml-title"><?php _e( 'Product tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                              name="settings[format_xml_product_tag]"
                                                                                                              value='<?php echo $settings['format_xml_product_tag'] ?>'><br>
                <span class="xml-title"><?php _e( 'Coupon tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                             name="settings[format_xml_coupon_tag]"
                                                                                                             value='<?php echo $settings['format_xml_coupon_tag'] ?>'><br>
                <span class="xml-title"><?php _e( 'Append XML', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                             name="settings[format_xml_append_raw_xml]"
                                                                                                             value='<?php echo $settings['format_xml_append_raw_xml'] ?>'><br>
                <span class="xml-title"><?php _e( 'Self closing tags', 'woo-order-export-lite' ) ?></span><input
                        type=checkbox name="settings[format_xml_self_closing_tags]"
                        value=1 <?php if ( @$settings['format_xml_self_closing_tags'] ) {
					echo 'checked';
				} ?> ><br>
            </div>
            <div id='JSON_options' style='display:none'><strong><?php _e( 'JSON options',
						'woo-order-export-lite' ) ?></strong><br>
                <span class="xml-title"><?php _e( 'Start tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                            name="settings[format_json_start_tag]"
                                                                                                            value='<?php echo @$settings['format_json_start_tag'] ?>'><br>
                <span class="xml-title"><?php _e( 'End tag', 'woo-order-export-lite' ) ?></span><input type=text
                                                                                                          name="settings[format_json_end_tag]"
                                                                                                          value='<?php echo @$settings['format_json_end_tag'] ?>'>
            </div>
            <div id='TSV_options' style='display:none'><strong><?php _e( 'TSV options',
						'woo-order-export-lite' ) ?></strong><br>
                <input type=hidden name="settings[format_tsv_add_utf8_bom]" value=0>
                <input type=hidden name="settings[format_tsv_display_column_names]" value=0>
                <input type=checkbox name="settings[format_tsv_add_utf8_bom]"
                       value=1 <?php if ( @$settings['format_tsv_add_utf8_bom'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Output UTF-8 BOM', 'woo-order-export-lite' ) ?><br>
                <input type=checkbox name="settings[format_tsv_display_column_names]"
                       value=1 <?php if ( @$settings['format_tsv_display_column_names'] ) {
					echo 'checked';
				} ?> > <?php _e( 'Output column titles as first line', 'woo-order-export-lite' ) ?><br>
				<?php _e( 'Line Break', 'woo-order-export-lite' ) ?><input type=text
                                                                              name="settings[format_tsv_linebreak]"
                                                                              value='<?php echo $settings['format_tsv_linebreak'] ?>'
                                                                              size=4><br>
				<?php if ( function_exists( 'iconv' ) ): ?>
					<?php _e( 'Character encoding', 'woo-order-export-lite' ) ?><input type=text
                                                                                          name="settings[format_tsv_encoding]"
                                                                                          value="<?php echo $settings['format_tsv_encoding'] ?>">
                    <br>
				<?php endif ?>
            </div>

            <div id='PDF_options' style='display:none'><strong><?php _e( 'PDF options',
				        'woo-order-export-lite' ) ?></strong><br>
                <input type=hidden name="settings[format_pdf_display_column_names]" value=0>
                <input type=checkbox name="settings[format_pdf_display_column_names]"
                       value=1 <?php if ( @$settings['format_pdf_display_column_names'] ) {
			        echo 'checked';
		        } ?> > <?php _e( 'Output column titles as first line', 'woo-order-export-lite' ) ?>

		        (
                <input type=hidden name="settings[format_pdf_repeat_header]" value=0>
                <input type=checkbox name="settings[format_pdf_repeat_header]"
                       value=1 <?php if ( @$settings['format_pdf_repeat_header'] ) {
		            echo 'checked';
	            } ?> > <?php _e( 'repeat at each page', 'woo-order-export-lite' ) ?>)<br>


              <div class="pdf_two_col_block">
	            <?php _e( 'Orientation', 'woo-order-export-lite' ) ?><br>
                <select name="settings[format_pdf_orientation]">
                    <?php foreach ( $pdf_format_available_options['orientation'] as $orientation => $label ): ?>
                        <option value="<?php echo $orientation; ?>" <?php echo selected($orientation == $settings['format_pdf_orientation']); ?> ><?php echo $label; ?></option>
                    <?php endforeach;?>
                </select>
              </div>

              <div class="pdf_two_col_block">
	            <?php _e( 'Page size', 'woo-order-export-lite' ) ?><br>
                <select name="settings[format_pdf_page_size]">
		            <?php foreach ( $pdf_format_available_options['page_size'] as $size => $label ): ?>
                        <option value="<?php echo $size; ?>" <?php echo selected($size == $settings['format_pdf_page_size']); ?> ><?php echo $label; ?></option>
		            <?php endforeach;?>
                </select>
              </div>

              <div class="pdf_two_col_block">
		        <?php _e( 'Font size', 'woo-order-export-lite' ) ?><br>
                <input type=number name="settings[format_pdf_font_size]" value='<?php echo $settings['format_pdf_font_size'] ?>' min=1 size=3><br>
              </div>

              <div class="pdf_two_col_block">
	            <?php _e( 'Page numbers', 'woo-order-export-lite' );

	            $align_types = array(
		            'L'       => __( 'Left align', 'woo-order-export-lite' ),
		            'C'       => __( 'Center align', 'woo-order-export-lite' ),
		            'R'       => __( 'Right align', 'woo-order-export-lite' ),
	            );

                ?><br>
                <select name="settings[format_pdf_pagination]">
	                <?php foreach ( array_merge( $align_types, array( 'disable' => __( 'No page numbers', 'woo-order-export-lite' ) ) ) as $align => $label ): ?>
                        <option value="<?php echo $align; ?>" <?php echo selected($align == $settings['format_pdf_pagination']); ?> ><?php echo $label; ?></option>
		            <?php endforeach;?>
                </select>
              </div>



              <div class="pdf_two_col_block">
	            <?php _e( 'Page header text', 'woo-order-export-lite' ) ?><br>
                <input type=text name="settings[format_pdf_header_text]" value='<?php echo $settings['format_pdf_header_text'] ?>'>
              </div>
              <div class="pdf_two_col_block">
	            <?php _e( 'Columns width', 'woo-order-export-lite' ) ?>
                <input title="<?php _e( 'comma separated list', 'woo-order-export-lite' ) ?>" type=text name="settings[format_pdf_cols_width]" value='<?php echo $settings['format_pdf_cols_width'] ?>'>
              </div>
              
              <div class="pdf_two_col_block">
	            <?php _e( 'Page footer text', 'woo-order-export-lite' ) ?><br>
                <input type=text name="settings[format_pdf_footer_text]" value='<?php echo $settings['format_pdf_footer_text'] ?>'>
              </div>
              <div class="pdf_two_col_block">
	            <?php _e( 'Columns align', 'woo-order-export-lite' ) ?>
                <input title="<?php _e( 'comma separated list', 'woo-order-export-lite' ) ?>" type=text name="settings[format_pdf_cols_align]" value='<?php echo $settings['format_pdf_cols_align'] ?>'>
              </div>


              <div class="pdf_two_col_block">
                 <?php _e( 'Fit table to page width', 'woo-order-export-lite' ) ?><br>
	            <input type="radio" name="settings[format_pdf_fit_page_width]" value=1 <?php checked( @$settings['format_pdf_fit_page_width'] ); ?> ><?php _e( 'Yes', 'woo-order-export-lite' ); ?>
	            <input type="radio" name="settings[format_pdf_fit_page_width]" value=0 <?php checked( !@$settings['format_pdf_fit_page_width'] ); ?> ><?php _e( 'No', 'woo-order-export-lite' ); ?>
              </div>
<hr>
              <div class="pdf_two_col_block">
	            <?php _e( 'Table header text color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_table_header_text_color]" value='<?php echo $settings['format_pdf_table_header_text_color'] ?>'>
              </div>
              <div class="pdf_two_col_block">
	            <?php _e( 'Table header background color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_table_header_background_color]" value='<?php echo $settings['format_pdf_table_header_background_color'] ?>'>
              </div>

              <div class="pdf_two_col_block">
	            <?php _e( 'Table row text color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_table_row_text_color]" value='<?php echo $settings['format_pdf_table_row_text_color'] ?>'>
              </div>
              <div class="pdf_two_col_block">
	            <?php _e( 'Table row background color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_table_row_background_color]" value='<?php echo $settings['format_pdf_table_row_background_color'] ?>'>
              </div>

              <div class="pdf_two_col_block">
	            <?php _e( 'Page header text color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_page_header_text_color]" value='<?php echo $settings['format_pdf_page_header_text_color'] ?>'>
              </div>
              <div class="pdf_two_col_block">
	            <?php _e( 'Page footer text color', 'woo-order-export-lite' ) ?>
                <input type=text class="color_pick" name="settings[format_pdf_page_footer_text_color]" value='<?php echo $settings['format_pdf_page_footer_text_color'] ?>'>
              </div>

                <hr>

                <div class="pdf_two_col_block">
                    <input type="button" class="button button-primary image-upload-button" value="<?php _e( 'Select Logo', 'woocommerce-pickingpal' ) ?>">
                    <input type="hidden" name="settings[format_pdf_logo_source]" value='<?php echo $settings['format_pdf_logo_source'] ?>'>
                    <br>
                    <?php $source = $settings['format_pdf_logo_source'] ? $settings['format_pdf_logo_source'] : '';?>
                    <img src="<?php echo $source; ?>" height="100" width="100" class="<?php echo ! $source ? 'hidden' : ''; ?>">
                    <br>
                    <input type="button" class="button button-warning image-clear-button <?php echo ! $source ? 'hidden' : ''; ?>" value="<?php _e( 'Remove logo', 'woocommerce-pickingpal' ) ?>">
                </div>
                <div class="pdf_two_col_block">
		            <?php _e( 'Logo align', 'woo-order-export-lite' ) ?>
                    <select name="settings[format_pdf_logo_align]">
			            <?php foreach ( $align_types as $align => $label ): ?>
                            <option value="<?php echo $align; ?>" <?php echo selected($align == $settings['format_pdf_logo_align']); ?> ><?php echo $label; ?></option>
			            <?php endforeach;?>
                    </select>
                </div>
                <div class="pdf_two_col_block">
		            <?php _e( 'Logo height', 'woo-order-export-lite' ) ?>
                    <br>
                    <input type="number" name="settings[format_pdf_logo_height]" value='<?php echo $settings['format_pdf_logo_height'] ?>' min="0">
                </div>
                <div class="pdf_two_col_block">
		            <?php _e( 'Logo width', 'woo-order-export-lite' ) ?> ( <?php _e( '0 - auto scale', 'woo-order-export-lite' ) ?> )
                    <br>
                    <input type="number" name="settings[format_pdf_logo_width]" value='<?php echo $settings['format_pdf_logo_width'] ?>' min="0">
                </div>

            </div>

            <hr>
            <div id="my-date-time-format" class="">
                <div id="date_format_block">
                    <span class="wc-oe-header"><?php _e( 'Date', 'woo-order-export-lite' ) ?></span>
					<?php
					$date_format = array(
						'',
						'F j, Y',
						'Y-m-d',
						'm/d/Y',
						'd/m/Y',
					);
					$date_format = apply_filters( 'woe_date_format', $date_format );
					?>
                    <select>
						<?php foreach ( $date_format as $format ): ?>
                            <option value="<?php echo $format ?>" <?php echo selected( @$settings['date_format'],
								$format ) ?> ><?php echo ! empty( $format ) ? current_time( $format ) : __( '-',
									'woo-order-export-lite' ) ?></option>
						<?php endforeach; ?>
                        <option value="custom" <?php echo selected( in_array( @$settings['date_format'], $date_format ),
							false ) ?> ><?php echo __( 'custom', 'woo-order-export-lite' ) ?></option>
                    </select>
                    <div id="custom_date_format_block" style="<?php echo in_array( @$settings['date_format'],
						$date_format ) ? 'display: none' : '' ?>">
                        <input type="text" name="settings[date_format]" value="<?php echo $settings['date_format'] ?>">
                    </div>
                </div>

                <div id="time_format_block">
                    <span class="wc-oe-header"><?php _e( 'Time', 'woo-order-export-lite' ) ?></span>
					<?php
					$time_format = array(
						'',
						'g:i a',
						'g:i A',
						'H:i',
					);
					$time_format = apply_filters( 'woe_time_format', $time_format );
					?>
                    <select>
						<?php foreach ( $time_format as $format ): ?>
                            <option value="<?php echo $format ?>" <?php echo selected( @$settings['time_format'],
								$format ) ?> ><?php echo ! empty( $format ) ? current_time( $format ) : __( '-',
									'woo-order-export-lite' ) ?></option>
						<?php endforeach; ?>
                        <option value="custom" <?php echo selected( in_array( @$settings['time_format'], $time_format ),
							false ) ?> ><?php echo __( 'custom', 'woo-order-export-lite' ) ?></option>
                    </select>
                    <div id="custom_time_format_block" style="<?php echo in_array( @$settings['time_format'],
						$time_format ) ? 'display: none' : '' ?>">
                        <input type="text" name="settings[time_format]" value="<?php echo $settings['time_format'] ?>">
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div id="my-sort" class="my-block">
			<?php
			$sort = array(
				'order_id'      => __( 'Order ID', 'woo-order-export-lite' ),
				'post_date'     => __( 'Order Date', 'woo-order-export-lite' ),
				'post_modified' => __( 'Modification Date', 'woo-order-export-lite' ),
			);
			ob_start();
			?>
            <select name="settings[sort]">
				<?php foreach ( $sort as $value => $text ): ?>
                    <option value='<?php echo $value ?>' <?php echo selected( @$settings['sort'],
						$value ) ?> ><?php echo $text; ?></option>
				<?php endforeach; ?>
            </select>
			<?php
			$sort_html = ob_get_clean();

			ob_start();
			?>
            <select name="settings[sort_direction]">
                <option value='DESC' <?php echo selected( @$settings['sort_direction'],
					'DESC' ) ?> ><?php _e( 'Descending', 'woo-order-export-lite' ) ?></option>
                <option value='ASC' <?php echo selected( @$settings['sort_direction'],
					'ASC' ) ?> ><?php _e( 'Ascending', 'woo-order-export-lite' ) ?></option>
            </select>
			<?php
			$sort_direction_html = ob_get_clean();

			echo sprintf( __( 'Sort orders by %s in %s order', 'woo-order-export-lite' ), $sort_html,
				$sort_direction_html );
			?>

			<?php if ( $mode === WC_Order_Export_Manage::EXPORT_SCHEDULE ): ?>
                <div>
                    <label for="change_order_status_to"><?php _e( 'Change order status to',
							'woo-order-export-lite' ) ?></label>
                    <select id="change_order_status_to" name="settings[change_order_status_to]">
                        <option value="" <?php if ( empty( $settings['change_order_status_to'] ) ) {
							echo 'selected';
						} ?>><?php _e( "- don't modify -", 'woo-order-export-lite' ) ?></option>
						<?php foreach ( wc_get_order_statuses() as $i => $status ) { ?>
                            <option value="<?php echo $i ?>" <?php if ( $i === $settings['change_order_status_to'] ) {
								echo 'selected';
							} ?>><?php echo $status ?></option>
						<?php } ?>
                    </select>
                </div>
			<?php endif; ?>
        </div>
        <br>
        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Misc settings', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-misc" hidden="hidden">
                <div>
                    <input type="hidden" name="settings[format_number_fields]" value="0"/>
                    <label><input type="checkbox" name="settings[format_number_fields]"
                                  value="1" <?php checked( $settings['format_number_fields'] ) ?>/><?php _e( 'Format numbers (use WC decimal separator)',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[export_all_comments]" value="0"/>
                    <label><input type="checkbox" name="settings[export_all_comments]"
                                  value="1" <?php checked( $settings['export_all_comments'] ) ?>/><?php _e( 'Export all order notes',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[export_refund_notes]" value="0"/>
                    <label><input type="checkbox" name="settings[export_refund_notes]"
                                  value="1" <?php checked( $settings['export_refund_notes'] ) ?>/><?php _e( 'Export refund notes as Customer Note',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[strip_tags_product_fields]" value="0"/>
                    <label><input type="checkbox" name="settings[strip_tags_product_fields]"
                                  value="1" <?php checked( $settings['strip_tags_product_fields'] ) ?>/><?php _e( 'Strip tags from Product Description/Variation',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[cleanup_phone]" value="0"/>
                    <label><input type="checkbox" name="settings[cleanup_phone]"
                                  value="1" <?php checked( $settings['cleanup_phone'] ) ?>/><?php _e( 'Cleanup phone (export only digits)',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[enable_debug]" value="0"/>
                    <label><input type="checkbox" name="settings[enable_debug]"
                                  value="1" <?php checked( $settings['enable_debug'] ) ?>/><?php _e( 'Enable debug output',
							'woo-order-export-lite' ) ?></label>
                </div>
                <div>
                    <input type="hidden" name="settings[custom_php]" value="0"/>
                    <label><input type="checkbox" name="settings[custom_php]"
                                  value="1" <?php checked( $settings['custom_php'] ) ?>/><?php _e( 'Custom PHP code to modify output',
							'woo-order-export-lite' ) ?></label>
                    <div id="custom_php_code_textarea" <?php echo $settings['custom_php'] ? '' : 'style="display: none"' ?>>
						<?php if ( $readonly_php == 'readonly' ): ?>
                            <strong>
								<?php _e( 'Please check permissions for your role. You must have capability “edit_themes” to use this box.',
									'woo-order-export-lite' ); ?>
                            </strong>
						<?php endif; ?>
                        <textarea placeholder="<?php _e( 'Use only unnamed functions!', 'woo-order-export-lite' ) ?>"
                                  name="settings[custom_php_code]" <?php echo $readonly_php ?> class="width-100"
                                  rows="10"><?php echo $settings['custom_php_code'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="my-right" style="float: left; width: 48%; margin: 0px 10px; max-width: 500px;">
		<?php
		if ( in_array( $mode,
			array( WC_Order_Export_Manage::EXPORT_SCHEDULE, WC_Order_Export_Manage::EXPORT_ORDER_ACTION ) ) ):
			include "pro-version/destinations.php";
		endif; ?>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by order', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-order" hidden="hidden">
                <div><input type="hidden" name="settings[skip_suborders]" value="0"/><label><input type="checkbox"
                                                                                                   name="settings[skip_suborders]"
                                                                                                   value="1" <?php checked( $settings['skip_suborders'] ) ?> /> <?php _e( "Don't export child orders",
							'woo-order-export-lite' ) ?></label></div>
                <div><input type="hidden" name="settings[export_refunds]" value="0"/><label><input type="checkbox"
                                                                                                   name="settings[export_refunds]"
                                                                                                   value="1" <?php checked( $settings['export_refunds'] ) ?> /> <?php _e( "Export refunds",
							'woo-order-export-lite' ) ?></label></div>
                <div><input type="hidden" name="settings[mark_exported_orders]" value="0"/><label><input type="checkbox"
                                                                                                         name="settings[mark_exported_orders]"
                                                                                                         value="1" <?php checked( $settings['mark_exported_orders'] ) ?> /> <?php _e( "Mark exported orders",
							'woo-order-export-lite' ) ?></label></div>
                <div><input type="hidden" name="settings[export_unmarked_orders]" value="0"/><label><input
                                type="checkbox" name="settings[export_unmarked_orders]"
                                value="1" <?php checked( $settings['export_unmarked_orders'] ) ?> /> <?php _e( "Export unmarked orders only",
							'woo-order-export-lite' ) ?></label></div>
                <span class="wc-oe-header"><?php _e( 'Order statuses', 'woo-order-export-lite' ) ?></span>
                <select id="statuses" name="settings[statuses][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php foreach (
						apply_filters( 'woe_settings_order_statuses', wc_get_order_statuses() ) as $i => $status
					) { ?>
                        <option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings['statuses'] ) ) {
							echo 'selected';
						} ?>><?php echo $status ?></option>
					<?php } ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Custom fields', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="custom_fields" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_order_custom_fields() as $cf_name ) { ?>
                        <option><?php echo $cf_name; ?></option>
					<?php } ?>
                </select>

                <select id="custom_fields_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                    <option>LIKE</option>
                    <option>&gt;</option>
                    <option>&gt;=</option>
                    <option>&lt;</option>
                    <option>&lt;=</option>
                    <option>NOT SET</option>
                    <option>IS SET</option>
                </select>

                <input type="text" id="text_custom_fields" disabled class="like-input" style="display: none;">

                <button id="add_custom_fields" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="custom_fields_check" multiple name="settings[order_custom_fields][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['order_custom_fields'] ) {
						foreach ( $settings['order_custom_fields'] as $prod ) {
							?>
                            <option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php }
					} ?>
                </select>

            </div>
        </div>

        <br>

        <div class="my-block">
            <div id=select2_warning
                 style='display:none;color:red;font-size: 120%;'><?php _e( "The filters won't work correctly.<br>Another plugin(or theme) has loaded outdated Select2.js",
					'woo-order-export-lite' ) ?></div>
            <span class="my-hide-next "><?php _e( 'Filter by product', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-products" hidden="hidden">
                <div><input type="hidden" name="settings[all_products_from_order]" value="0"/><label><input
                                type="checkbox" name="settings[all_products_from_order]"
                                value="1" <?php checked( $settings['all_products_from_order'] ) ?> /> <?php _e( 'Export all products from the order',
							'woo-order-export-lite' ) ?></label></div>
                <div><input type="hidden" name="settings[skip_refunded_items]" value="0"/><label><input type="checkbox"
                                                                                                        name="settings[skip_refunded_items]"
                                                                                                        value="1" <?php checked( $settings['skip_refunded_items'] ) ?> /> <?php _e( 'Skip fully refunded items',
							'woo-order-export-lite' ) ?></label></div>
                <span class="wc-oe-header"><?php _e( 'Product categories', 'woo-order-export-lite' ) ?></span>
                <select id="product_categories" name="settings[product_categories][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_categories'] ) {
						foreach ( $settings['product_categories'] as $cat ) {
							$cat_term = get_term( $cat, 'product_cat' );
							if ( $cat_term ) {
								?>
                                <option selected
                                        value="<?php echo $cat_term->term_id ?>"> <?php echo $cat_term->name; ?></option>
								<?php
							}
							?>
						<?php }
					} ?>
                </select>
                <span class="wc-oe-header"><?php _e( 'Vendor/creator', 'woo-order-export-lite' ) ?></span>
                <select id="product_vendors" name="settings[product_vendors][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_vendors'] ) {
						foreach ( $settings['product_vendors'] as $user_id ) {
							$user = get_user_by( 'id', $user_id );
							?>
                            <option selected value="<?php echo $user_id ?>"> <?php echo $user->display_name; ?></option>
						<?php }
					} ?>
                </select>

				<?php do_action( "woe_settings_filter_by_product_after_vendors", $settings ); ?>

                <span class="wc-oe-header"><?php _e( 'Product', 'woo-order-export-lite' ) ?></span>

                <select id="products" name="settings[products][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['products'] ) {
						foreach ( $settings['products'] as $prod ) {
							$p = get_the_title( $prod );
							?>
                            <option selected value="<?php echo $prod ?>"> <?php echo $p; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Product taxonomies', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="taxonomies" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_product_taxonomies() as $attr_id => $attr_name ) { ?>
                        <option><?php echo $attr_name; ?></option>
					<?php } ?>
                </select>

                <select id="taxonomies_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                </select>

                <input type="text" id="text_taxonomies" disabled style="display: none;">

                <button id="add_taxonomies" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span>
                </button>
                <br>
                <select id="taxonomies_check" multiple name="settings[product_taxonomies][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_taxonomies'] ) {
						foreach ( $settings['product_taxonomies'] as $prod ) {
							?>
                            <option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Product custom fields', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="product_custom_fields" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_product_custom_fields() as $cf_name ) { ?>
                        <option><?php echo $cf_name; ?></option>
					<?php } ?>
                </select>

                <select id="product_custom_fields_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                    <option>LIKE</option>
                    <option>&gt;</option>
                    <option>&gt;=</option>
                    <option>&lt;</option>
                    <option>&lt;=</option>
                </select>

                <input type="text" id="text_product_custom_fields" disabled class="like-input" style="display: none;">

                <button id="add_product_custom_fields" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="product_custom_fields_check" multiple name="settings[product_custom_fields][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_custom_fields'] ) {
						foreach ( $settings['product_custom_fields'] as $prod ) {
							?>
                            <option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Variable product attributes',
						'woo-order-export-lite' ) ?></span>
                <br>
                <select id="attributes" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_product_attributes() as $attr_id => $attr_name ) { ?>
                        <option><?php echo $attr_name; ?></option>
					<?php } ?>
                </select>

                <select id="attributes_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                    <option>LIKE</option>
                </select>

                <input type="text" id="text_attributes" disabled class="like-input" style="display: none;">

                <button id="add_attributes" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span>
                </button>
                <br>
                <select id="attributes_check" multiple name="settings[product_attributes][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_attributes'] ) {
						foreach ( $settings['product_attributes'] as $prod ) {
							?>
                            <option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Item meta data', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="itemmeta" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_product_itemmeta() as $attr_name ) { ?>
                        <option data-base64="<?php echo base64_encode( $attr_name ); ?>"><?php echo $attr_name; ?></option>
					<?php } ?>
                </select>

                <select id="itemmeta_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                    <option>LIKE</option>
                    <option>&gt;</option>
                    <option>&gt;=</option>
                    <option>&lt;</option>
                    <option>&lt;=</option>
                </select>

                <input type="text" id="text_itemmeta" disabled class="like-input" style="display: none;">

                <button id="add_itemmeta" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span>
                </button>
                <br>
                <select id="itemmeta_check" multiple name="settings[product_itemmeta][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['product_itemmeta'] ) {
						foreach ( $settings['product_itemmeta'] as $prod ) {
							?>
                            <option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php }
					} ?>
                </select>

            </div>
        </div>

        <br>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by customers', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-users" hidden="hidden">

                <span class="wc-oe-header"><?php _e( 'Usernames', 'woo-order-export-lite' ) ?></span>
                <select id="user_names" name="settings[user_names][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['user_names'] ) {
						foreach ( $settings['user_names'] as $user_id ) {
							$user = get_user_by( 'id', $user_id );
							?>
                            <option selected value="<?php echo $user_id ?>"> <?php echo $user->display_name; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'User roles', 'woo-order-export-lite' ) ?></span>
                <select id="user_roles" name="settings[user_roles][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					global $wp_roles;
					foreach ( $wp_roles->role_names as $k => $v ) { ?>
                        <option value="<?php echo $k ?>" <?php echo( in_array( $k,
							$settings['user_roles'] ) ? selected( true ) : '' ) ?>> <?php echo $v ?></option>
					<?php } ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Custom fields', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="user_custom_fields" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_user_custom_fields() as $cf_name ) { ?>
                        <option><?php echo $cf_name; ?></option>
					<?php } ?>
                </select>
                <select id="user_custom_fields_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                    <option>LIKE</option>
                    <option>&gt;</option>
                    <option>&gt;=</option>
                    <option>&lt;</option>
                    <option>&lt;=</option>
                    <option>NOT SET</option>
                    <option>IS SET</option>
                </select>

                <input type="text" id="text_user_custom_fields" disabled class="like-input" style="display: none;">

                <button id="add_user_custom_fields" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="user_custom_fields_check" multiple name="settings[user_custom_fields][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( ! empty( $settings['user_custom_fields'] ) ) {
						foreach ( $settings['user_custom_fields'] as $value ) {
							?>
                            <option selected value="<?php echo $value; ?>"> <?php echo $value; ?></option>
						<?php }
					} ?>
                </select>
            </div>
        </div>

        <br>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by coupons', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-coupons" hidden="hidden">
                <div>
                    <input type="hidden" name="settings[any_coupon_used]" value="0"/>
                    <label><input type="checkbox" name="settings[any_coupon_used]"
                                  value="1" <?php checked( $settings['any_coupon_used'] ) ?>/><?php _e( 'Any coupon used',
							'woo-order-export-lite' ) ?></label>
                </div>
                <span class="wc-oe-header"><?php _e( 'Coupons', 'woo-order-export-lite' ) ?></span>
                <select id="coupons" name="settings[coupons][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['coupons'] ) {
						foreach ( $settings['coupons'] as $coupon ) {
							?>
                            <option selected value="<?php echo $coupon; ?>"> <?php echo $coupon; ?></option>
						<?php }
					} ?>
                </select>
            </div>
        </div>

        <br>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by billing', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-billing" hidden="hidden">
                <span class="wc-oe-header"><?php _e( 'Billing locations', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="billing_locations">
                    <option>City</option>
                    <option>State</option>
                    <option>Postcode</option>
                    <option>Country</option>
                </select>
                <select id="billing_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                </select>

                <button id="add_billing_locations" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="billing_locations_check" multiple name="settings[billing_locations][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['billing_locations'] ) {
						foreach ( $settings['billing_locations'] as $location ) {
							?>
                            <option selected value="<?php echo $location; ?>"> <?php echo $location; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Payment methods', 'woo-order-export-lite' ) ?></span>
                <select id="payment_methods" name="settings[payment_methods][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) { ?>
                        <option value="<?php echo $gateway->id ?>" <?php if ( in_array( $gateway->id,
							$settings['payment_methods'] ) ) {
							echo 'selected';
						} ?>><?php echo $gateway->get_title() ?></option>
					<?php } ?>
                </select>
            </div>
        </div>

        <br>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by shipping', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-shipping" hidden="hidden">
                <span class="wc-oe-header"><?php _e( 'Shipping locations', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="shipping_locations">
                    <option>City</option>
                    <option>State</option>
                    <option>Postcode</option>
                    <option>Country</option>
                </select>
                <select id="shipping_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                </select>

                <button id="add_shipping_locations" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="shipping_locations_check" multiple name="settings[shipping_locations][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['shipping_locations'] ) {
						foreach ( $settings['shipping_locations'] as $location ) {
							?>
                            <option selected value="<?php echo $location; ?>"> <?php echo $location; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Shipping methods', 'woo-order-export-lite' ) ?></span>
                <select id="shipping_methods" name="settings[shipping_methods][]" multiple="multiple"
                        style="width: 100%; max-width: 25%;">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_shipping_methods() as $i => $title ) { ?>
                        <option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings['shipping_methods'] ) ) {
							echo 'selected';
						} ?>><?php echo $title ?></option>
					<?php } ?>
                </select>
            </div>
        </div>

        <br>

        <div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by item and metadata', 'woo-order-export-lite' ) ?>
                <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
            <div id="my-items-meta" hidden="hidden">
                <span class="wc-oe-header"><?php _e( 'Item names', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="item_names">
                    <option>coupon</option>
                    <option>fee</option>
                    <option>line_item</option>
                    <option>shipping</option>
                    <option>tax</option>
                </select>
                <select id="item_name_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                </select>
                <button id="add_item_names" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span>
                </button>
                <br>
                <select id="item_names_check" multiple name="settings[item_names][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['item_names'] ) {
						foreach ( $settings['item_names'] as $name ) {
							?>
                            <option selected value="<?php echo $name; ?>"> <?php echo $name; ?></option>
						<?php }
					} ?>
                </select>

                <span class="wc-oe-header"><?php _e( 'Item metadata', 'woo-order-export-lite' ) ?></span>
                <br>
                <select id="item_metadata">
					<?php foreach ( WC_Order_Export_Data_Extractor_UI::get_item_meta_keys() as $type => $meta_keys ) { ?>
                        <optgroup label="<?php echo ucwords( $type ); ?>">
							<?php foreach ( $meta_keys as $item_meta_key ) { ?>
                                <option value="<?php echo $type . ":" . $item_meta_key; ?>"><?php echo $item_meta_key; ?></option>
							<?php } ?>
                        </optgroup>
					<?php } ?>
                </select>
                <select id="item_metadata_compare" class="select_compare">
                    <option>=</option>
                    <option>&lt;&gt;</option>
                </select>
                <button id="add_item_metadata" class="button-secondary"><span
                            class="dashicons dashicons-plus-alt"></span></button>
                <br>
                <select id="item_metadata_check" multiple name="settings[item_metadata][]"
                        style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['item_metadata'] ) {
						foreach ( $settings['item_metadata'] as $meta ) {
							?>
                            <option selected value="<?php echo $meta; ?>"> <?php echo $meta; ?></option>
						<?php }
					} ?>
                </select>

            </div>
        </div>

    </div>

    <div class="clearfix"></div>
    <br>
    <div class="my-block">
		<span id='adjust-fields-btn' class="my-hide-next "><?php _e( 'Set up fields to export',
				'woo-order-export-lite' ) ?>
            <span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
        <div id="manage_fields" style="display: none;">
            <div style="display: grid; grid-template-columns: 10fr 1fr 10fr;">
                <div class="clear"></div>
                <div></div>
                <div>
                    <br class="clear"/>
                </div>
                <div id='fields' style='display:none;'>
                    <div class="fields-control-block"></div>
                    <br>
                    <div class="fields-control">
                        <div style="display: inline-block; float: left">
                            <label style="font-size: medium;">
								<?php _e( 'Drag rows to reorder exported fields', 'woo-order-export-lite' ) ?>
                            </label>
                        </div>
                        <div style="display: inline-block; float: right; margin-bottom: 15px">
                            <a id="clear_selected_fields" class="button"
                               style="background-color: #bb77ae; color: white;: ">
								<?php _e( 'Remove all fields', 'woo-order-export-lite' ) ?>
                            </a>
                        </div>
                    </div>
                    <div>
                        <br class="clear"/>
                    </div>
                    <ul id="order_fields"></ul>
                </div>
                <div></div>
                <div id='unselected_fields'>
                    <ul class="subsubsub">
						<?php $segments = WC_Order_Export_Data_Extractor_UI::get_order_segments(); ?>
						<?php foreach ( $segments as $id => $segment_title ): ?>
                            <li>
                                <a class="segment_choice"
                                   data-segment="<?php echo $id; ?>" href="#segment=<?php echo $id; ?>">
									<?php echo $segment_title; ?>
                                </a>
								<?php echo( end( $segments ) == $segment_title ? '' : ' | ' ); ?>
                            </li>
						<?php endforeach; ?>
                    </ul>
                    <br class="clear">
                    <div class="tab-controls">
                        <div class="tab-actions-buttons">
                            <span class="tab-actions-buttons__title">
                                <strong><?php _e( 'Actions', 'woo-order-export-lite' ) ?>:</strong>
                            </span>
                            <button class='button-secondary add-meta'>
								<?php _e( 'Add field', 'woo-order-export-lite' ) ?>
                            </button>
                            <button class='button-secondary add-custom'>
								<?php _e( 'Add static field', 'woo-order-export-lite' ) ?>
                            </button>
                        </div>
                        <div class="tab-actions-forms">
                            <div class='div_meta segment-form all-segments'>
                                <label for="select_custom_meta_order">
									<?php _e( 'Meta key', 'woo-order-export-lite' ) ?>:
                                </label><br/>
                                <select id='select_custom_meta_order'>
									<?php
									foreach ( $order_custom_meta_fields as $meta_id => $meta_name ) {
										echo "<option value='$meta_name' >$meta_name</option>";
									};
									?>
                                </select>
                                <div id="custom_meta_order_mode" style="margin-bottom: 10px;">
                                    <input style="width: 80%;" type='text' id='text_custom_meta_order'
                                           placeholder="<?php _e( 'or type meta key here',
										       'woo-order-export-lite' ) ?>"/><br>
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <input id="custom_meta_order_mode_used" type="checkbox"
                                           name="custom_meta_order_mode" value="used"> <?php _e( 'Hide unused fields',
										'woo-order-export-lite' ) ?>
                                </div>
                                <hr>
                                <div style="margin-top: 20px;"><label for="colname_custom_meta"><?php _e( 'Column name',
											'woo-order-export-lite' ) ?>:</label><input type='text'
                                                                                           id='colname_custom_meta'/>
                                </div>
                                <div style="margin-top: 20px;">
									<?php echo print_formats_field( 'meta' ); ?>
                                </div>
                                <div style="text-align: right;">
                                    <button id='button_custom_meta' class='button-secondary'><?php _e( 'Confirm',
											'woo-order-export-lite' ) ?></button>
                                    <button class='button-secondary button-cancel'><?php _e( 'Cancel',
											'woo-order-export-lite' ) ?></button>
                                </div>
                            </div>
                            <div class='div_custom segment-form all-segments'>
                                <div>
                                    <label for="colname_custom_field"><?php _e( 'Column name',
											'woo-order-export-lite' ) ?>:</label>
                                    <input type='text' id='colname_custom_field'/>
                                </div>
                                <div>
                                    <label for="value_custom_field"><?php _e( 'Value', 'woo-order-export-lite' ) ?>
                                        :</label>
                                    <input type='text' id='value_custom_field'/>
                                </div>
                                <div>
									<?php echo print_formats_field( 'field' ); ?>
                                </div>
                                <div style="text-align: right;">
                                    <button id='button_custom_field' class='button-secondary'><?php _e( 'Confirm',
											'woo-order-export-lite' ) ?></button>
                                    <button class='button-secondary button-cancel'><?php _e( 'Cancel',
											'woo-order-export-lite' ) ?></button>
                                </div>
                            </div>
                            <div class='div_meta products-segment segment-form products-add-field'>
                                <div id="custom_meta_products_mode" class="hide">
                                    <label><input id="custom_meta_products_mode_used" type="checkbox"
                                                  name="custom_meta_products_mode"
                                                  value="used"> <?php _e( 'Hide unused fields',
											'woo-order-export-lite' ) ?></label>
                                </div>
                                <label for="select_custom_meta_products"><?php _e( 'Product fields',
										'woo-order-export-lite' ) ?>:</label><select
                                        id='select_custom_meta_products'></select>

                                <label for="select_custom_meta_order_items"><?php _e( 'Order item fields',
										'woo-order-export-lite' ) ?>:</label><select
                                        id='select_custom_meta_order_items'></select>
                                <label>&nbsp;</label><input style="width: 80%;" type='text'
                                                            id='text_custom_meta_order_items'
                                                            placeholder="<?php _e( 'or type meta key here',
									                            'woo-order-export-lite' ) ?>"/><br>
                                <div style="width: 80%; text-align: center;"><?php _e( 'OR',
										'woo-order-export-lite' ) ?></div>
                                <label><?php _e( 'Taxonomy', 'woo-order-export-lite' ) ?>:</label><select
                                        id='select_custom_taxonomies_products'>
                                    <option></option>
									<?php
									foreach ( WC_Order_Export_Data_Extractor_UI::get_product_taxonomies() as $tax_id => $tax_name ) {
										echo "<option value='__$tax_name' >__$tax_name</option>";
									};
									?>
                                </select>
                                <hr>
                                <div style="margin-top: 15px;"></div>
                                <label><?php _e( 'Column name', 'woo-order-export-lite' ) ?>:</label><input
                                        type='text' id='colname_custom_meta_products'/>
                                <div style="margin-top: 15px;"></div>
								<?php echo print_formats_field( 'meta', 'products' ); ?>
                                <div style="text-align: right;">
                                    <button id='button_custom_meta_products'
                                            class='button-secondary'><?php _e( 'Confirm',
											'woo-order-export-lite' ) ?></button>
                                    <button class='button-secondary button-cancel'><?php _e( 'Cancel',
											'woo-order-export-lite' ) ?></button>
                                </div>
                            </div>
                            <div class='div_custom products-segment segment-form products-add-static-field'>
                                <div>
                                    <label for="colname_custom_field_products"><?php _e( 'Column name',
											'woo-order-export-lite' ) ?>:</label>
                                    <input type='text' id='colname_custom_field_products'/>
                                </div>
                                <div>
                                    <label for="value_custom_field_products"><?php _e( 'Value',
											'woo-order-export-lite' ) ?>:</label>
                                    <input type='text' id='value_custom_field_products'/>
                                </div>
                                <div>
									<?php echo print_formats_field( 'field', 'products' ); ?>
                                </div>
                                <div style="text-align: right;">
                                    <button id='button_custom_field_products'
                                            class='button-secondary'><?php _e( 'Confirm',
											'woo-order-export-lite' ) ?></button>
                                    <button class='button-secondary button-cancel'><?php _e( 'Cancel',
											'woo-order-export-lite' ) ?></button>
                                </div>
                            </div>
                            <div class='div_meta coupons-segment segment-form coupons-add-field'>
                                <label><?php _e( 'Meta key', 'woo-order-export-lite' ) ?>:</label>
                                <div id="custom_meta_coupons_mode" style="display: none;">
                                    <input id="custom_meta_coupons_mode" type="checkbox"
                                           name="custom_meta_coupons_mode" value="used"> <?php _e( 'Hide unused fields',
		                                'woo-order-export-lite' ) ?>
                                </div>
                                <br>
                                <select id='select_custom_meta_coupons'></select>
                                <input style="width: 80%;margin-bottom: 10px;" type='text' id='text_custom_meta_coupons'
                                       placeholder="<?php _e( 'or type meta key here',
									       'woo-order-export-lite' ) ?>"/><br/>
                                <hr>
                                <label><?php _e( 'Column name', 'woo-order-export-lite' ) ?>:</label><input
                                        type='text' id='colname_custom_meta_coupons'/></label>
                                <div style="margin-top: 20px;">
									<?php echo print_formats_field( 'meta', 'coupons' ); ?>
                                </div>
                                <div style="text-align: right;">
                                    <button id='button_custom_meta_coupons'
                                            class='button-secondary'><?php _e( 'Confirm',
											'woo-order-export-lite' ) ?></button>
                                    <button class='button-secondary button-cancel'><?php _e( 'Cancel',
											'woo-order-export-lite' ) ?></button>
                                </div>
                            </div>
                            <div class='div_custom coupons-segment segment-form coupons-add-static-field'>
                                <div>
                                    <label for="colname_custom_field_coupons"><?php _e( 'Column name',
											'woo-order-export-lite' ) ?>:</label>
                                    <input type='text' id='colname_custom_field_coupons'/>
                                </div>
                                <div>
                                    <label for="value_custom_field_coupons"><?php _e( 'Value',
											'woo-order-export-lite' ) ?>:</label>
                                    <input type='text' id='value_custom_field_coupons'/>
                                </div>
                                <div>
									<?php echo print_formats_field( 'field', 'coupons' ); ?>
                                </div>
                                <div style="text-align: right;">
                                    <button id='button_custom_field_coupons' class='button-secondary'>
										<?php _e( 'Confirm', 'woo-order-export-lite' ) ?>
                                    </button>
                                    <button class='button-secondary button-cancel'>
										<?php _e( 'Cancel', 'woo-order-export-lite' ) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="unselected_fields_list"></div>


                    <!--                    <div class="section settings-segment" id="order_segment">-->
                    <!--                        <h1>ORDER</h1>-->
                    <!--                    </div>-->
                    <!--                    <div class="section settings-segment" id="products_segment">-->
                    <!--                        <h1>PRODUCT</h1>-->
                    <!--                    </div>-->
                    <!--                    <div class="section settings-segment" id="coupons_segment">-->
                    <!--                        <h1>COUPON</h1>-->
                    <!--                    </div>-->

                </div>
            </div>
            <div id="modal_content" style="display: none;"></div>
        </div>

    </div>
	<?php do_action( "woe_settings_above_buttons", $settings ); ?>
    <div id=JS_error_onload
         style='color:red;font-size: 120%;'><?php echo sprintf( __( "If you see this message after page load, user interface won't work correctly!<br>There is a JS error (<a target=blank href='%s'>read here</a> how to view it). Probably, it's a conflict with another plugin or active theme.",
			'woo-order-export-lite' ),
			"https://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors#Step_3:_Diagnosis" ); ?></div>
    <p class="submit">
        <input type="submit" id='preview-btn' class="button-secondary preview-btn"
               data-limit="<?php echo( $mode === WC_Order_Export_Manage::EXPORT_ORDER_ACTION ? 1 : 5 ); ?>"
               value="<?php _e( 'Preview', 'woo-order-export-lite' ) ?>"
               title="<?php _e( 'Might be different from actual export!', 'woo-order-export-lite' ) ?>"/>
		<?php if ( $mode == WC_Order_Export_Manage::EXPORT_NOW ): ?>
            <input type="submit" id='save-only-btn' class="button-primary"
                   value="<?php _e( 'Save settings', 'woo-order-export-lite' ) ?>"/>
		<?php else: ?>
            <input type="submit" id='save-btn' class="button-primary"
                   value="<?php _e( 'Save & Exit', 'woo-order-export-lite' ) ?>"/>
            <input type="submit" id='save-only-btn' class="button-secondary"
                   value="<?php _e( 'Save settings', 'woo-order-export-lite' ) ?>"/>
		<?php endif; ?>

		<?php if ( $show['export_button'] ) { ?>
            <input type="submit" id='export-btn' class="button-secondary"
                   value="<?php _e( 'Export', 'woo-order-export-lite' ) ?>"/>
		<?php } ?>
		<?php if ( $show['export_button_plain'] ) { ?>
            <input type="submit" id='export-wo-pb-btn' class="button-secondary"
                   value="<?php _e( 'Export [w/o progressbar]', 'woo-order-export-lite' ) ?>"
                   title="<?php _e( 'It might not work for huge datasets!', 'woo-order-export-lite' ) ?>"/>
		<?php } ?>
		<?php if ( $mode === WC_Order_Export_Manage::EXPORT_NOW && $WC_Order_Export::is_full_version() ): ?>
            <input type="submit" id='copy-to-profiles' class="button-secondary"
                   value="<?php _e( 'Save as a profile', 'woo-order-export-lite' ) ?>"/>
		<?php endif; ?>
		
		<?php if ( $mode === WC_Order_Export_Manage::EXPORT_NOW ): ?>
            <input type="submit" id='reset-profile' class="button-secondary"
                   value="<?php _e( 'Reset settings', 'woo-order-export-lite' ) ?>"/>
		<?php endif; ?>
                   
        <span id="preview_actions" class="hide">
			<strong id="output_preview_total"><?php echo sprintf( __( 'Export total: %s orders',
					'woo-order-export-lite' ), '<span></span>' ) ?></strong>
			<?php _e( 'Preview size', 'woo-order-export-lite' ); ?>
			<?php foreach ( array( 5, 10, 25, 50 ) as $n ): ?>
                <button class="button-secondary preview-btn" data-limit="<?php echo $n; ?>"><?php echo $n; ?></button>
			<?php endforeach ?>
		</span>
    </p>
    <div id=Settings_updated
         style='display:none;color:green;font-size: 120%;'><?php _e( "Settings were successfully updated!",
			'woo-order-export-lite' ) ?></div>

	<?php if ( $show['export_button'] OR $show['export_button_plain'] ) { ?>
        <div id="progress_div" style="display: none;">
            <h1 class="title-cancel"><?php _e( "Press 'Esc' to cancel the export", 'woo-order-export-lite' ) ?></h1>
            <h1 class="title-download"><a target=_blank><?php _e( "Click here to download",
						'woo-order-export-lite' ) ?></a></h1>
            <div id="progressBar">
                <div></div>
            </div>
        </div>
        <div id="background"></div>
	<?php } ?>

</form>
<textarea rows=10 id='output_preview' style="overflow: auto;" wrap='off'></textarea>
<div id='output_preview_csv' style="overflow: auto;width:100%"></div>

<iframe id='export_new_window_frame' width=0 height=0 style='display:none'></iframe>

<form id='export_wo_pb_form' method='post' target='export_wo_pb_window'>
    <input name="action" type="hidden" value="order_exporter">
    <input name="method" type="hidden" value="plain_export">
    <?php wp_nonce_field( 'woe_nonce', 'woe_nonce' ); ?>
    <input name="mode" type="hidden" value="<?php echo $mode ?>">
    <input name="id" type="hidden" value="<?php echo $id ?>">
    <input name="json" type="hidden">
</form>