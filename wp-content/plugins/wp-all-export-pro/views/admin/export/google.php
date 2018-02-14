<?php
$productAttributesJson = empty(XmlExportEngine::$globalAvailableSections['product_data']['additional']['attributes']['meta']) ? '' : json_encode(XmlExportEngine::$globalAvailableSections['product_data']['additional']['attributes']['meta']);

if(getenv('WPAE_DEV')) {
    wp_enqueue_script('pmxe-livereload', '//localhost:35729/livereload.js', array(), '3', true);
}
?>
<script type="text/javascript">
    var wpae_product_attributes = <?php echo empty($productAttributesJson) ? '""' : $productAttributesJson; ?>;
</script>

<div ng-app="GoogleMerchants"
     ng-controller="mainController"
     ng-init="init('<?php if (class_exists("WooCommerce")) echo get_woocommerce_currency_symbol(); ?>',
     '<?php if (class_exists("WooCommerce")) echo  get_woocommerce_currency();?>',
     <?php $is_template_loaded = PMXE_Plugin::$session->get('is_loaded_template'); if(!empty($is_template_loaded)) { echo PMXE_Plugin::$session->get('is_loaded_template'); } else { echo "false"; } ?>)"
     class="googleMerchants" id="googleMerchants">
    <?php
    if ($post['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS && $post['export_to'] == XmlExportEngine::EXPORT_TYPE_XML) {
        ?>
        <span ng-init="selectGoogleMerchantsInitially()"></span>
    <?php
    }
    ?>
    <div ng-slide-down="isGoogleMerchantExport" duration="0.5">
        <basic-information information="merchantsFeedData.basicInformation"></basic-information>
        <availability-price information="merchantsFeedData.availabilityPrice"></availability-price>
        <product-categories information="merchantsFeedData.productCategories"></product-categories>
        <unique-identifiers information="merchantsFeedData.uniqueIdentifiers"></unique-identifiers>
        <detailed-information information="merchantsFeedData.detailedInformation"></detailed-information>
        <shipping information="merchantsFeedData.shipping"></shipping>
        <advanced-attributes information="merchantsFeedData.advancedAttributes"></advanced-attributes>
    </div>
</div>