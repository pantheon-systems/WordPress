<?php
if (!defined('ABSPATH')) {
    exit;
}

$ftp_server = '';
$ftp_user = '';
$ftp_password = '';
$use_ftps = '';
$enable_ftp_ie = '';
$ftp_server_path = '';
if (!empty($ftp_settings)) {
    $ftp_server = $ftp_settings['ftp_server'];
    $ftp_user = $ftp_settings['ftp_user'];
    $ftp_password = $ftp_settings['ftp_password'];
    $use_ftps = $ftp_settings['use_ftps'];
    $enable_ftp_ie = $ftp_settings['enable_ftp_ie'];
    $ftp_server_path = $ftp_settings['ftp_server_path'];
}
?>
<div class="woocommerce">

    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex'); ?>" class="nav-tab"><?php _e('Product Export', 'wf_csv_import_export'); ?></a>
        <a href="<?php echo admin_url('admin.php?import=xa_woocommerce_csv'); ?>" class="nav-tab nav-tab-active"><?php _e('Product Import', 'wf_csv_import_export'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex&tab=help'); ?>" class="nav-tab"><?php _e('Help', 'wf_csv_import_export'); ?></a>
        <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="nav-tab nav-tab-premium"><?php _e('Upgrade to Premium for More Features', 'wf_csv_import_export'); ?></a>
    </h2>

    <div class="pipe-main-box">
        <div class="pipe-view bg-white p-20p">
            <h3 class="title"><?php _e('Step 1: Import settings', 'wf_csv_import_export'); ?></h3>
            <?php if (!empty($upload_dir['error'])) : ?>
                <div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'wf_csv_import_export'); ?></p>
                    <p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
            <?php else : ?>
                <div class="tool-box">
                    <p><?php _e('You can import products (in CSV format) in to the shop using by uploading a CSV file.', 'wf_csv_import_export'); ?></p>
                    <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th>
                                        <label for="upload"><?php _e('Select a file from your computer', 'wf_csv_import_export'); ?></label>
                                    </th>
                                    <td>
                                        <input type="file" id="upload" name="import" size="25" />
                                        <input type="hidden" name="action" value="save" />
                                        <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" /><br/>
                                        <small><?php _e('Please upload UTF-8 encoded CSV', 'wf_csv_import_export'); ?> &nbsp; -- &nbsp; <?php printf(__('Maximum size: %s', 'wf_csv_import_export'), $size); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Update products if exists', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td>
                                        <input type="checkbox" name="merge" id="merge">
                                        <p><small><?php _e('Existing products are identified by their SKUs/IDs. If this option is not selected and if a product with same ID/SKU is found in the CSV, that product will not be imported.', 'wf_csv_import_export'); ?></small></p>
                                    </td>
                            
                                </tr>
                                <tr>
                                    <th><label><?php _e('Delimiter', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td><input type="text" name="delimiter" placeholder="," size="2" /></td>
                                </tr>
                                <tr>
                                    <th><label><?php _e('Merge empty cells', 'wf_csv_import_export'); ?></label><br/></th>
                                    <td><input type="checkbox" name="merge_empty_cells" placeholder="," size="2" />
                                        <p><small><?php _e('If this option is checked, empty attributes will be added to products with no value. You can leave this unchecked if you are not sure about this option.', 'wf_csv_import_export'); ?></small></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" class="button button-primary" value="<?php esc_attr_e('Proceed to Import Mapping', 'wf_csv_import_export'); ?>" />
                            <p><span><i><?php _e('If you want to import from an FTP location or from a URL or to configure a scheduled import you may need to upgrade to premium version.', 'wf_csv_import_export'); ?></i></span></p>
                        </p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php include(WF_ProdImpExpCsv_BASE . 'includes/views/market.php'); ?>
        <div class="clearfix"></div>
    </div>
</div>