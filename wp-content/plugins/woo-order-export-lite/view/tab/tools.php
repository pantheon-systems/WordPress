<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$modes = array(
    WC_Order_Export_Manage::EXPORT_NOW,
    WC_Order_Export_Manage::EXPORT_PROFILE,
    WC_Order_Export_Manage::EXPORT_SCHEDULE,
    WC_Order_Export_Manage::EXPORT_ORDER_ACTION
);

$settings_export = array();
$setting_names = array();
foreach ($modes as $mode) {
    $setting_name = WC_Order_Export_Manage::get_settings_name_for_mode($mode);
    $setting_names[ $mode ] = $setting_name;
    $settings_export[ $mode ] = WC_Order_Export_Manage::get( $mode );
}

$type_labels = !$WC_Order_Export::is_full_version() ? array() : array(
	WC_Order_Export_Manage::EXPORT_PROFILE      => __( 'Profiles', 'woo-order-export-lite' ),
	WC_Order_Export_Manage::EXPORT_ORDER_ACTION => __( 'Status change jobs', 'woo-order-export-lite' ),
	WC_Order_Export_Manage::EXPORT_SCHEDULE     => __( 'Scheduled jobs', 'woo-order-export-lite' ),
);
?>
<div class="clearfix"></div>
<div id="woe-admin" class="container-fluid wpcontent">
    <form>
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h2 class="woe-box-title"><?php _e( 'Export settings', 'woo-order-export-lite' ) ?></h2>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <h6 class="woe-fake-label"><?php _e( 'Copy these settings and use it to migrate plugin to another WordPress install.', 'woo-order-export-lite' ) ?></h6>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <select id="tools-export-selector">
                            <option data-json='<?php echo json_encode( $settings_export, JSON_PRETTY_PRINT|JSON_HEX_APOS ) ?>'><?php _e( 'All', 'woo-order-export-lite' ) ?></option>
                            <option data-json='<?php echo  json_encode( $settings_export[ WC_Order_Export_Manage::EXPORT_NOW ], JSON_PRETTY_PRINT|JSON_HEX_APOS ) ?>'>
		                        <?php _e( 'Export now', 'woo-order-export-lite' ) ?></option>
		                    <?php foreach ( $type_labels as $group => $label ): ?>
                                <optgroup label="<?php echo $label ?>"></optgroup>
                                <?php foreach ( $settings_export[ $group ] as $item_id => $item ): ?>
                                    <option data-json='<?php echo json_encode( $item, JSON_PRETTY_PRINT|JSON_HEX_APOS ) ?>'>
                                        <?php echo $item_id . " - " . ( isset( $item['title'] ) ? $item['title'] : '' ) ?></option>
                                <?php endforeach; ?>
		                    <?php endforeach; ?>
                        </select>
                        <textarea rows="7" id="tools-export-text" class='tools-textarea'></textarea>
                        <p class="help-block"><?php _e( 'Just click inside the textarea and copy (Ctrl+C)', 'woo-order-export-lite' ) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form method="post">
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h2 class="woe-box-title"><?php _e( 'Import settings', 'woo-order-export-lite' ) ?></h2>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <h6 class="woe-fake-label"><?php _e( 'Paste text into this field to import settings into the current WordPress install.', 'woo-order-export-lite' ) ?></h6>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <textarea rows="7" id="tools-import-text" name="tools-import"></textarea>
                        <p class="help-block"><?php _e( 'This process will overwrite your settings for "Advanced Order Export For WooCommerce" !', 'woo-order-export-lite' ) ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 form-group col-md-offset-7">
                        <input  disabled type="submit" class="woe-btn-tools" value="<?php _e( 'Import', 'woo-order-export-lite' ) ?>" name="woe-tools-import" id="submit-import">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery( function ( $ ) {
        jQuery( '#wpbody .tools-textarea' ).click( function () {
            jQuery( this ).select();
        } );

        jQuery( '#tools-import-text' ).on( 'input propertychange', function () {
            var $textarea = jQuery( this ).val();
            var disable = ( $textarea.length == '' );
            $( "#submit-import" ).prop( "disabled", disable );
        } );

        jQuery( '#submit-import' ).on( 'click', function ( e ) {
            if ( !confirm( '<?php esc_attr_e( 'Are you sure to continue?', 'woo-order-export-lite' ) ?>' ) ) {
                e.preventDefault();
                $( document.activeElement ).blur();
            } else {
                var data = $( '#woe-admin form' ).serialize();
                data = data + "&action=order_exporter&method=save_tools";
                $.post( ajaxurl, data, function ( response ) {
                    document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=tools&save=y' ) ?>';
                }, "json" );
                return false;
            }
        } );

        jQuery( '#tools-export-selector' ).change( function() {
            jQuery( '#tools-export-text' ).val( jQuery( this ).find( ':selected' ).attr( 'data-json' ) );
        } ).change();

    } );
</script>