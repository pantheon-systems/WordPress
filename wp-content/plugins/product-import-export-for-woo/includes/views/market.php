<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="market-box table-box-main">
    <div class="getting-started-video">
        <h2><?php _e('Watch getting started video', 'wf_csv_import_export');?></h2>
    <iframe src="https://www.youtube.com/embed/L-01qI1EZWE?rel=0&showinfo=0" frameborder="0" allowfullscreen="allowfullscreen" align="center"></iframe>
    </div>
    <div class="pipe-review-widget">
        <?php
        echo sprintf(__('<div class=""><p><i>If you like the plugin please leave us a %1$s review!</i><p></div>', 'wf_csv_import_export'), '<a href="https://wordpress.org/support/plugin/product-import-export-for-woo/reviews?rate=5#new-post" target="_blank" class="xa-pipe-rating-link" data-reviewed="' . esc_attr__('Thanks for the review.', 'wf_csv_import_export') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>');
        ?>
    </div>
    <div class="pipe-premium-features">
        <ul style="font-weight: bold; color:#666; list-style: none; background:#f8f8f8; padding:20px; margin:20px 15px; font-size: 15px; line-height: 26px;">
                <li style=""><?php echo __('30 Day Money Back Guarantee','cookie-law-info'); ?></li>
                <li style=""><?php echo __('Fast and Superior Support','cookie-law-info'); ?></li>
                <li style="">
                    <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="button button-primary button-go-pro"><?php _e('Upgrade to Premium', 'wf_csv_import_export'); ?></a>
                </li>
            </ul>
            <span>
        <ul class="ticked-list">
            <li><?php _e('Export/Import simple, group, external and variation products.', 'wf_csv_import_export');?></li>
            <li><?php _e('Export products by category.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import/Export product reviews.', 'wf_csv_import_export');?></li>
            <li><?php _e('Various filter options for exporting products.', 'wf_csv_import_export');?> </li>
            <li><?php _e('Map and transform fields during import.', 'wf_csv_import_export');?></li>
            <li><?php _e('Manipulate/evaluate data during import.', 'wf_csv_import_export');?></li>
            <li><?php _e('Choice to update or skip existing imported products.', 'wf_csv_import_export');?></li>
            <li><?php _e('WPML support for simple products.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import/Export file via FTP.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import from URL.', 'wf_csv_import_export');?></li>
            <li><?php _e('Automatic scheduled import and export.', 'wf_csv_import_export');?></li>
            <li><?php _e('Supports product reviews export and import.', 'wf_csv_import_export');?></li>
            <li><?php _e('Third party plugin customization support.', 'wf_csv_import_export');?></li>            
        </ul>
    </span>
    <center> 
        <a href="https://www.webtoffee.com/category/documentation/product-import-export-plugin-for-woocommerce/" target="_blank" class="button button-doc-demo"><?php _e('Documentation', 'wf_csv_import_export'); ?></a></center>
    </div>
    
    </div>
