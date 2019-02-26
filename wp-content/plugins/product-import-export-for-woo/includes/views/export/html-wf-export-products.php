<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script>
    jQuery(document).ready(function (a) {
    "use strict";
            // Listen for click on toggle checkbox
            jQuery('#pselectall').live("click", function () {
                // Iterate each checkbox
                jQuery(':checkbox').each(function () {
                    this.checked = true;
                });
            });
            jQuery('#punselectall').live("click", function () {
                // Iterate each checkbox
                jQuery(':checkbox').each(function () {
                    this.checked = false;
                });
            });
        });
</script>
<div class="pipe-main-box">
    <div class="tool-box bg-white p-20p pipe-view">
        <h3 class="title" style="font-size: 1.3em !important;font-weight: 600;"><?php _e('Export Settings', 'wf_csv_import_export'); ?></h3>
        <p><?php _e('Export and download your products in CSV file format. This file can be used to import products back into your WooCommerce store.', 'wf_csv_import_export'); ?></p>
        <form action="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex&action=export'); ?>" method="post">
            <table class="form-table">
                <tr>
                    <th>
                        <label for="v_offset"><?php _e('Offset', 'wf_csv_import_export'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="offset" id="v_offset" placeholder="0" class="input-text" />
                        <p style="font-size: 12px"><?php _e('Number of products to skip before exporting. If the value is 0 no products are skipped. If value is 100, products from product id 101 will be exported.', 'wf_csv_import_export'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="v_limit"><?php _e('Limit', 'wf_csv_import_export'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="limit" id="v_limit" placeholder="<?php _e('Unlimited', 'wf_csv_import_export'); ?>" class="input-text" />
                        <p style="font-size: 12px"><?php _e('Number of products to export. If no value is given all products will be exported. This is useful if you have large number of products and want to export partial list of products.', 'wf_csv_import_export'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <label for="v_columns"><?php _e('Columns', 'wf_csv_import_export'); ?></label>
                        <p style="font-size: 12px;font-weight:300;"><?php _e('Configure the Column Names of CSV file.', 'wf_csv_import_export'); ?></p>
                    </th>
                <table id="datagrid">
                    <th style="text-align: left; padding:6px 25px !important; font-weight: normal !important;color:#000;">
                        <label for="v_columns"><?php _e('WooCommerce product field name', 'wf_csv_import_export'); ?></label>
                    </th>
                    <th style="text-align: left; padding:6px 25px !important; font-weight: normal !important;color:#000;">
                        <label for="v_columns_name"><?php _e('Column header name in the CSV file', 'wf_csv_import_export'); ?></label>
                    </th>
                    <!-- select all boxes -->
                        <tr>
                            <td style="padding: 10px;">
                                <a href="#" id="pselectall" onclick="return false;" ><?php _e('Select all', 'wf_csv_import_export'); ?></a> &nbsp;/&nbsp;
                                <a href="#" id="punselectall" onclick="return false;"><?php _e('Unselect all', 'wf_csv_import_export'); ?></a>
                            </td>
                        </tr>
                    <?php
                    $post_columns['images'] = 'Images (featured and gallery)';
                    $post_columns['file_paths'] = 'Downloadable file paths';
                    $post_columns['taxonomies'] = 'Taxonomies (cat/tags/shipping-class)';
                    $post_columns['attributes'] = 'Attributes';
                    ?>
                    <?php foreach ($post_columns as $pkey => $pcolumn) {
                        ?>
                        <tr>
                            <td>
                                <input name= "columns[<?php echo $pkey; ?>]" type="checkbox" value="<?php echo $pkey; ?>" checked>
                                <label for="columns[<?php echo $pkey; ?>]"><?php _e($pcolumn, 'wf_csv_import_export'); ?></label>
                            </td>
                            <td>
                                <?php
                                $tmpkey = $pkey;
                                if (strpos($pkey, 'yoast') === false) {
                                    $tmpkey = ltrim($pkey, '_');
                                }
                                ?>
                                <input type="text" name="columns_name[<?php echo $pkey; ?>]"  value="<?php echo $tmpkey; ?>" class="input-text" />
                            </td>
                        </tr>
                    <?php } ?>

                </table><br/>
                </tr>
            </table>
            <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Export Products', 'wf_csv_import_export'); ?>" /></p>
            <p><span><i><?php _e('With this version of the plugin you can export products (except variable products) in to a file. If you want to export to an FTP location (scheduled / manual ) you may need to upgrade to premium version.', 'wf_csv_import_export'); ?></i></span></p>
        </form>
    </div>
    <?php include(WF_ProdImpExpCsv_BASE . 'includes/views/market.php'); ?>
<div class="clearfix"></div>
</div>