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
    <center><a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank" class="button button-primary button-go-pro"><?php _e('Upgrade to Premium Version', 'wf_csv_import_export'); ?></a></center>
    <span>
        <ul>
            <li><?php _e('Export simple, group, external and variation products.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import simple, group, external and variation products.', 'wf_csv_import_export');?></li>
            <li><?php _e('Export products by category.', 'wf_csv_import_export');?></li>
            <li><?php _e('Various filter options for exporting products.', 'wf_csv_import_export');?> </li>
            <li><?php _e('Map and transform fields while importing products.', 'wf_csv_import_export');?></li>
            <li><?php _e('Change values while importing products using evaluation fields.', 'wf_csv_import_export');?></li>
            <li><?php _e('Choice to update or skip existing imported products.', 'wf_csv_import_export');?></li>
            <li><?php _e('WPML supported. French and German support out of the box.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import/Export file via FTP.', 'wf_csv_import_export');?></li>
            <li><?php _e('Import from URL.', 'wf_csv_import_export');?></li>
            <li><?php _e('Automatic scheduled import and export.', 'wf_csv_import_export');?></li>
            <li><?php _e('Supports product reviews export and import.', 'wf_csv_import_export');?></li>
            <li><?php _e('30 Days Money Back Guarantee.', 'wf_csv_import_export');?></li>
            <li><?php _e('More frequent plugin updates.', 'wf_csv_import_export');?></li>
            <li><?php _e('Excellent Support for setting it up!', 'wf_csv_import_export');?></li>
        </ul>
    </span>
    <center> 
        <a href="http://productimportexport.webtoffee.com/" target="_blank" class="button button-doc-demo"><?php _e('Live Demo', 'wf_csv_import_export'); ?></a> 
        <a href="https://www.webtoffee.com/category/documentation/product-import-export-plugin-for-woocommerce/" target="_blank" class="button button-doc-demo"><?php _e('Documentation', 'wf_csv_import_export'); ?></a></center>
    </div>
    
    </div>
