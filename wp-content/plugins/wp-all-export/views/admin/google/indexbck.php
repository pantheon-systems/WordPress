<?php
wp_enqueue_script('pmxe-angular-app', PMXE_ROOT_URL . '/frontend/dist/app.js', array('jquery'), PMXE_VERSION);
wp_enqueue_style('pmxe-angular-scss', PMXE_ROOT_URL . '/frontend/dist/styles.css', array(), PMXE_VERSION);

if(getenv('WPAE_DEV')) {
    // Livereload in dev mode
    echo '<script src="//localhost:35729/livereload.js"></script>';
}
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        alert('test');
    });
</script>
<div ng-app="GoogleMerchants" ng-controller="mainController" ng-init="init()">

</div>
<?php
?>

<div ng-app="GoogleMerchants" ng-controller="mainController" ng-init="init()">
    <div class="wpallexport-header">
        <div class="wpallexport-logo"></div>
        <div class="wpallexport-title">
            <p>WP All Export</p>
            <h2>Export to XML / CSV</h2>
        </div>
        <div class="wpallexport-links">
            <a href="http://www.wpallimport.com/support/" target="_blank">Support</a> | <a href="http://www.wpallimport.com/documentation/" target="_blank">Documentation</a>
        </div>
    </div>
    <div class="clear"></div>
<table class="wpallexport-layout wpallexport-export-template" style="margin-top: 15px;">
    <tbody>
        <tr>
            <td class="left">
                <div class="wpallexport-upload-resource-step-one rad4" style="padding-left:0;">
                    <div class="wpallexport-collapsed-header">
                        <h3 style="color: #425e99; padding-left: 25px;">Product Categories</h3>
                    </div>
                    <category-mapper cats="cats"></category-mapper>
                </div>
            </td>
            <td class="right template-sidebar" style="position: relative; width: 18%; right: 0px; padding: 0;">

                <fieldset id="available_data" class="optionsset rad4">

                    <div class="title">Available Data</div>

                    <div class="wpallexport-xml resetable" style="max-height: 865px;">

                        <ul>


                            <p class="wpae-available-fields-group">Standard<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="ui-draggable ui-draggable-handle">
                                        <div class="default_column" rel="">
                                            <label class="wpallexport-element-label">All Standard</label>
                                            <input type="hidden" name="rules[]" value="pmxe_default">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="1">
                                            <label class="wpallexport-xml-element">ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="id">
                                            <input type="hidden" name="cc_value[]" value="id">
                                            <input type="hidden" name="cc_name[]" value="ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="2">
                                            <label class="wpallexport-xml-element">Title</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="title">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="title">
                                            <input type="hidden" name="cc_value[]" value="title">
                                            <input type="hidden" name="cc_name[]" value="Title">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="3">
                                            <label class="wpallexport-xml-element">Content</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="content">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="content">
                                            <input type="hidden" name="cc_value[]" value="content">
                                            <input type="hidden" name="cc_name[]" value="Content">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="4">
                                            <label class="wpallexport-xml-element">Excerpt</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="excerpt">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="excerpt">
                                            <input type="hidden" name="cc_value[]" value="excerpt">
                                            <input type="hidden" name="cc_name[]" value="Excerpt">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="5">
                                            <label class="wpallexport-xml-element">Date</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="date">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="date">
                                            <input type="hidden" name="cc_value[]" value="date">
                                            <input type="hidden" name="cc_name[]" value="Date">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="6">
                                            <label class="wpallexport-xml-element">Post Type</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="post_type">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="post_type">
                                            <input type="hidden" name="cc_value[]" value="post_type">
                                            <input type="hidden" name="cc_name[]" value="Post Type">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_default wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="7">
                                            <label class="wpallexport-xml-element">Permalink</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="permalink">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="permalink">
                                            <input type="hidden" name="cc_value[]" value="permalink">
                                            <input type="hidden" name="cc_name[]" value="Permalink">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <p class="wpae-available-fields-group">Product Data<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="ui-draggable ui-draggable-handle">
                                        <div class="default_column" rel="">
                                            <label class="wpallexport-element-label">All Product Data</label>
                                            <input type="hidden" name="rules[]" value="pmxe_product_data">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="8">
                                            <label class="wpallexport-xml-element">SKU</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_sku">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_sku">
                                            <input type="hidden" name="cc_name[]" value="SKU">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="9">
                                            <label class="wpallexport-xml-element">Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_price">
                                            <input type="hidden" name="cc_name[]" value="Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="10">
                                            <label class="wpallexport-xml-element">Regular Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_regular_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_regular_price">
                                            <input type="hidden" name="cc_name[]" value="Regular Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="11">
                                            <label class="wpallexport-xml-element">Sale Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_sale_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_sale_price">
                                            <input type="hidden" name="cc_name[]" value="Sale Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="12">
                                            <label class="wpallexport-xml-element">Stock Status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_stock_status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_stock_status">
                                            <input type="hidden" name="cc_name[]" value="Stock Status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="13">
                                            <label class="wpallexport-xml-element">Stock</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_stock">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_stock">
                                            <input type="hidden" name="cc_name[]" value="Stock">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="14">
                                            <label class="wpallexport-xml-element">Visibility</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_visibility">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_visibility">
                                            <input type="hidden" name="cc_name[]" value="Visibility">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="15">
                                            <label class="wpallexport-xml-element">External Product URL</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_product_url">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_product_url">
                                            <input type="hidden" name="cc_name[]" value="External Product URL">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="16">
                                            <label class="wpallexport-xml-element">Total Sales</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="total_sales">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="total_sales">
                                            <input type="hidden" name="cc_name[]" value="Total Sales">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle" style="display: none;">
                                        <div class="custom_column" rel="17">
                                            <label class="wpallexport-xml-element">Attributes</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="attributes">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="attributes">
                                            <input type="hidden" name="cc_name[]" value="Attributes">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_product_data wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="18">
                                            <label class="wpallexport-xml-element">Shipping Class</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="product_shipping_class">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cats">
                                            <input type="hidden" name="cc_value[]" value="product_shipping_class">
                                            <input type="hidden" name="cc_name[]" value="Shipping Class">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="available_sub_section">
                                        <p class="wpae-available-fields-group">Attributes<span class="wpae-expander">+</span></p>
                                        <div class="wpae-custom-field">
                                            <ul>
                                                <li class="ui-draggable ui-draggable-handle">
                                                    <div class="default_column" rel="">
                                                        <label class="wpallexport-element-label">All Attributes</label>
                                                        <input type="hidden" name="rules[]" value="pmxe_product_data_attributes">
                                                    </div>
                                                </li>
                                                <li class="pmxe_product_data_attributes  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="19">
                                                        <label class="wpallexport-xml-element">Colot</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="pa_colot">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="0">
                                                        <input type="hidden" name="cc_type[]" value="attr">
                                                        <input type="hidden" name="cc_value[]" value="pa_colot">
                                                        <input type="hidden" name="cc_name[]" value="Colot">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_product_data_attributes  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="20">
                                                        <label class="wpallexport-xml-element">Size</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="pa_size">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="0">
                                                        <input type="hidden" name="cc_type[]" value="attr">
                                                        <input type="hidden" name="cc_value[]" value="pa_size">
                                                        <input type="hidden" name="cc_name[]" value="Size">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_product_data_attributes  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="21">
                                                        <label class="wpallexport-xml-element">Size</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="attribute_size">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="0">
                                                        <input type="hidden" name="cc_type[]" value="cf">
                                                        <input type="hidden" name="cc_value[]" value="attribute_size">
                                                        <input type="hidden" name="cc_name[]" value="Size">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                            </ul>
                                        </div></li>
                                </ul>
                            </div>

                            <p class="wpae-available-fields-group">Media<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="available_sub_section">
                                        <p class="wpae-available-fields-group">Images<span class="wpae-expander">+</span></p>
                                        <div class="wpae-custom-field">
                                            <ul>
                                                <li class="ui-draggable ui-draggable-handle">
                                                    <div class="default_column" rel="">
                                                        <label class="wpallexport-element-label">All Images</label>
                                                        <input type="hidden" name="rules[]" value="pmxe_media_images">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="22">
                                                        <label class="wpallexport-xml-element">URL</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="url">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_url">
                                                        <input type="hidden" name="cc_value[]" value="url">
                                                        <input type="hidden" name="cc_name[]" value="URL">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="23">
                                                        <label class="wpallexport-xml-element">Filename</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="filename">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_filename">
                                                        <input type="hidden" name="cc_value[]" value="filename">
                                                        <input type="hidden" name="cc_name[]" value="Filename">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="24">
                                                        <label class="wpallexport-xml-element">Path</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="path">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_path">
                                                        <input type="hidden" name="cc_value[]" value="path">
                                                        <input type="hidden" name="cc_name[]" value="Path">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="25">
                                                        <label class="wpallexport-xml-element">ID</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="image_id">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_id">
                                                        <input type="hidden" name="cc_value[]" value="image_id">
                                                        <input type="hidden" name="cc_name[]" value="ID">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="26">
                                                        <label class="wpallexport-xml-element">Title</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="title">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_title">
                                                        <input type="hidden" name="cc_value[]" value="title">
                                                        <input type="hidden" name="cc_name[]" value="Title">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="27">
                                                        <label class="wpallexport-xml-element">Caption</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="caption">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_caption">
                                                        <input type="hidden" name="cc_value[]" value="caption">
                                                        <input type="hidden" name="cc_name[]" value="Caption">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="28">
                                                        <label class="wpallexport-xml-element">Description</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="description">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_description">
                                                        <input type="hidden" name="cc_value[]" value="description">
                                                        <input type="hidden" name="cc_name[]" value="Description">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_images wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="29">
                                                        <label class="wpallexport-xml-element">Alt Text</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="alt">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="image_alt">
                                                        <input type="hidden" name="cc_value[]" value="alt">
                                                        <input type="hidden" name="cc_name[]" value="Alt Text">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                            </ul>
                                        </div></li>
                                    <li class="available_sub_section">
                                        <p class="wpae-available-fields-group">Attachments<span class="wpae-expander">+</span></p>
                                        <div class="wpae-custom-field">
                                            <ul>
                                                <li class="ui-draggable ui-draggable-handle">
                                                    <div class="default_column" rel="">
                                                        <label class="wpallexport-element-label">All Attachments</label>
                                                        <input type="hidden" name="rules[]" value="pmxe_media_attachments">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="30">
                                                        <label class="wpallexport-xml-element">URL</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="url">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_url">
                                                        <input type="hidden" name="cc_value[]" value="url">
                                                        <input type="hidden" name="cc_name[]" value="URL">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="31">
                                                        <label class="wpallexport-xml-element">Filename</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="filename">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_filename">
                                                        <input type="hidden" name="cc_value[]" value="filename">
                                                        <input type="hidden" name="cc_name[]" value="Filename">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="32">
                                                        <label class="wpallexport-xml-element">Path</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="path">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_path">
                                                        <input type="hidden" name="cc_value[]" value="path">
                                                        <input type="hidden" name="cc_name[]" value="Path">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="33">
                                                        <label class="wpallexport-xml-element">ID</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="attachment_id">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_id">
                                                        <input type="hidden" name="cc_value[]" value="attachment_id">
                                                        <input type="hidden" name="cc_name[]" value="ID">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="34">
                                                        <label class="wpallexport-xml-element">Title</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="title">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_title">
                                                        <input type="hidden" name="cc_value[]" value="title">
                                                        <input type="hidden" name="cc_name[]" value="Title">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="35">
                                                        <label class="wpallexport-xml-element">Caption</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="caption">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_caption">
                                                        <input type="hidden" name="cc_value[]" value="caption">
                                                        <input type="hidden" name="cc_name[]" value="Caption">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="36">
                                                        <label class="wpallexport-xml-element">Description</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="description">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_description">
                                                        <input type="hidden" name="cc_value[]" value="description">
                                                        <input type="hidden" name="cc_name[]" value="Description">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                                <li class="pmxe_media_attachments  ui-draggable ui-draggable-handle">
                                                    <div class="custom_column" rel="37">
                                                        <label class="wpallexport-xml-element">Alt Text</label>
                                                        <input type="hidden" name="ids[]" value="1">
                                                        <input type="hidden" name="cc_label[]" value="alt">
                                                        <input type="hidden" name="cc_php[]" value="0">
                                                        <input type="hidden" name="cc_code[]" value="0">
                                                        <input type="hidden" name="cc_sql[]" value="0">
                                                        <input type="hidden" name="cc_options[]" value="{&quot;is_export_featured&quot;:true,&quot;is_export_attached&quot;:true,&quot;image_separator&quot;:&quot;|&quot;}">
                                                        <input type="hidden" name="cc_type[]" value="attachment_alt">
                                                        <input type="hidden" name="cc_value[]" value="alt">
                                                        <input type="hidden" name="cc_name[]" value="Alt Text">
                                                        <input type="hidden" name="cc_settings[]" value="">
                                                    </div>
                                                </li>
                                            </ul>
                                        </div></li>
                                </ul>
                            </div>

                            <p class="wpae-available-fields-group">Taxonomies<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="ui-draggable ui-draggable-handle">
                                        <div class="default_column" rel="">
                                            <label class="wpallexport-element-label">All Taxonomies</label>
                                            <input type="hidden" name="rules[]" value="pmxe_cats">
                                        </div>
                                    </li>
                                    <li class="pmxe_cats wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="38">
                                            <label class="wpallexport-xml-element">Product Type</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="product_type">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cats">
                                            <input type="hidden" name="cc_value[]" value="product_type">
                                            <input type="hidden" name="cc_name[]" value="Product Type">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_cats wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="39">
                                            <label class="wpallexport-xml-element">Product Categories</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="product_cat">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cats">
                                            <input type="hidden" name="cc_value[]" value="product_cat">
                                            <input type="hidden" name="cc_name[]" value="Product Categories">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_cats wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="40">
                                            <label class="wpallexport-xml-element">Product Tags</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="product_tag">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cats">
                                            <input type="hidden" name="cc_value[]" value="product_tag">
                                            <input type="hidden" name="cc_name[]" value="Product Tags">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <p class="wpae-available-fields-group">Custom Fields<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="ui-draggable ui-draggable-handle">
                                        <div class="default_column" rel="">
                                            <label class="wpallexport-element-label">All Custom Fields</label>
                                            <input type="hidden" name="rules[]" value="pmxe_cf">
                                        </div>
                                    </li>
                                    <li class="pmxe_cf  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="41">
                                            <label class="wpallexport-xml-element">_wp_old_slug</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wp_old_slug">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cf">
                                            <input type="hidden" name="cc_value[]" value="_wp_old_slug">
                                            <input type="hidden" name="cc_name[]" value="_wp_old_slug">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_cf  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="42">
                                            <label class="wpallexport-xml-element">_wp_trash_meta_status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wp_trash_meta_status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cf">
                                            <input type="hidden" name="cc_value[]" value="_wp_trash_meta_status">
                                            <input type="hidden" name="cc_name[]" value="_wp_trash_meta_status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_cf  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="43">
                                            <label class="wpallexport-xml-element">_wp_trash_meta_time</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wp_trash_meta_time">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cf">
                                            <input type="hidden" name="cc_value[]" value="_wp_trash_meta_time">
                                            <input type="hidden" name="cc_name[]" value="_wp_trash_meta_time">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_cf  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="44">
                                            <label class="wpallexport-xml-element">_wp_desired_post_slug</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wp_desired_post_slug">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="cf">
                                            <input type="hidden" name="cc_value[]" value="_wp_desired_post_slug">
                                            <input type="hidden" name="cc_name[]" value="_wp_desired_post_slug">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <p class="wpae-available-fields-group">Other<span class="wpae-expander">+</span></p>
                            <div class="wpae-custom-field">
                                <ul>
                                    <li class="ui-draggable ui-draggable-handle">
                                        <div class="default_column" rel="">
                                            <label class="wpallexport-element-label">All Other</label>
                                            <input type="hidden" name="rules[]" value="pmxe_other">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="45">
                                            <label class="wpallexport-xml-element">Status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="status">
                                            <input type="hidden" name="cc_value[]" value="status">
                                            <input type="hidden" name="cc_name[]" value="Status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="46">
                                            <label class="wpallexport-xml-element">Author</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="author">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="author">
                                            <input type="hidden" name="cc_value[]" value="author">
                                            <input type="hidden" name="cc_name[]" value="Author">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="47">
                                            <label class="wpallexport-xml-element">Slug</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="slug">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="slug">
                                            <input type="hidden" name="cc_value[]" value="slug">
                                            <input type="hidden" name="cc_name[]" value="Slug">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="48">
                                            <label class="wpallexport-xml-element">Format</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="format">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="format">
                                            <input type="hidden" name="cc_value[]" value="format">
                                            <input type="hidden" name="cc_name[]" value="Format">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="49">
                                            <label class="wpallexport-xml-element">Template</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="template">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="template">
                                            <input type="hidden" name="cc_value[]" value="template">
                                            <input type="hidden" name="cc_name[]" value="Template">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="50">
                                            <label class="wpallexport-xml-element">Parent</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="parent">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="parent">
                                            <input type="hidden" name="cc_value[]" value="parent">
                                            <input type="hidden" name="cc_name[]" value="Parent">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="51">
                                            <label class="wpallexport-xml-element">Order</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="order">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="order">
                                            <input type="hidden" name="cc_value[]" value="order">
                                            <input type="hidden" name="cc_name[]" value="Order">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="52">
                                            <label class="wpallexport-xml-element">Comment Status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="comment_status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="comment_status">
                                            <input type="hidden" name="cc_value[]" value="comment_status">
                                            <input type="hidden" name="cc_name[]" value="Comment Status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="53">
                                            <label class="wpallexport-xml-element">Ping Status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="ping_status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="ping_status">
                                            <input type="hidden" name="cc_value[]" value="ping_status">
                                            <input type="hidden" name="cc_name[]" value="Ping Status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="54">
                                            <label class="wpallexport-xml-element">Downloadable</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_downloadable">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_downloadable">
                                            <input type="hidden" name="cc_name[]" value="Downloadable">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="55">
                                            <label class="wpallexport-xml-element">Virtual</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_virtual">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_virtual">
                                            <input type="hidden" name="cc_name[]" value="Virtual">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="56">
                                            <label class="wpallexport-xml-element">Purchase Note</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_purchase_note">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_purchase_note">
                                            <input type="hidden" name="cc_name[]" value="Purchase Note">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="57">
                                            <label class="wpallexport-xml-element">Featured</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_featured">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_featured">
                                            <input type="hidden" name="cc_name[]" value="Featured">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="58">
                                            <label class="wpallexport-xml-element">Weight</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_weight">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_weight">
                                            <input type="hidden" name="cc_name[]" value="Weight">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="59">
                                            <label class="wpallexport-xml-element">Length</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_length">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_length">
                                            <input type="hidden" name="cc_name[]" value="Length">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="60">
                                            <label class="wpallexport-xml-element">Width</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_width">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_width">
                                            <input type="hidden" name="cc_name[]" value="Width">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="61">
                                            <label class="wpallexport-xml-element">Height</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_height">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_height">
                                            <input type="hidden" name="cc_name[]" value="Height">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="62">
                                            <label class="wpallexport-xml-element">Sale Price Dates From</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_sale_price_dates_from">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_sale_price_dates_from">
                                            <input type="hidden" name="cc_name[]" value="Sale Price Dates From">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="63">
                                            <label class="wpallexport-xml-element">Sale Price Dates To</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_sale_price_dates_to">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_sale_price_dates_to">
                                            <input type="hidden" name="cc_name[]" value="Sale Price Dates To">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="64">
                                            <label class="wpallexport-xml-element">Sold IndivIDually</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_sold_individually">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_sold_individually">
                                            <input type="hidden" name="cc_name[]" value="Sold IndivIDually">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="65">
                                            <label class="wpallexport-xml-element">Manage Stock</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_manage_stock">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_manage_stock">
                                            <input type="hidden" name="cc_name[]" value="Manage Stock">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="66">
                                            <label class="wpallexport-xml-element">Up-Sells</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_upsell_ids">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_upsell_ids">
                                            <input type="hidden" name="cc_name[]" value="Up-Sells">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="67">
                                            <label class="wpallexport-xml-element">Cross-Sells</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_crosssell_ids">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_crosssell_ids">
                                            <input type="hidden" name="cc_name[]" value="Cross-Sells">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="68">
                                            <label class="wpallexport-xml-element">Downloadable Files</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_downloadable_files">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_downloadable_files">
                                            <input type="hidden" name="cc_name[]" value="Downloadable Files">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="69">
                                            <label class="wpallexport-xml-element">Download Limit</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_download_limit">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_download_limit">
                                            <input type="hidden" name="cc_name[]" value="Download Limit">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="70">
                                            <label class="wpallexport-xml-element">Download Expiry</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_download_expiry">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_download_expiry">
                                            <input type="hidden" name="cc_name[]" value="Download Expiry">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="71">
                                            <label class="wpallexport-xml-element">Download Type</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_download_type">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_download_type">
                                            <input type="hidden" name="cc_name[]" value="Download Type">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="72">
                                            <label class="wpallexport-xml-element">Button Text</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_button_text">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_button_text">
                                            <input type="hidden" name="cc_name[]" value="Button Text">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="73">
                                            <label class="wpallexport-xml-element">Backorders</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_backorders">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_backorders">
                                            <input type="hidden" name="cc_name[]" value="Backorders">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="74">
                                            <label class="wpallexport-xml-element">Tax Status</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_tax_status">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_tax_status">
                                            <input type="hidden" name="cc_name[]" value="Tax Status">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="75">
                                            <label class="wpallexport-xml-element">Tax Class</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_tax_class">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_tax_class">
                                            <input type="hidden" name="cc_name[]" value="Tax Class">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="76">
                                            <label class="wpallexport-xml-element">Product Image Gallery</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_product_image_gallery">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_product_image_gallery">
                                            <input type="hidden" name="cc_name[]" value="Product Image Gallery">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="77">
                                            <label class="wpallexport-xml-element">Default Attributes</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_default_attributes">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_default_attributes">
                                            <input type="hidden" name="cc_name[]" value="Default Attributes">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="78">
                                            <label class="wpallexport-xml-element">Product Attributes</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_product_attributes">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_product_attributes">
                                            <input type="hidden" name="cc_name[]" value="Product Attributes">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="79">
                                            <label class="wpallexport-xml-element">Product Version</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_product_version">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_product_version">
                                            <input type="hidden" name="cc_name[]" value="Product Version">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="80">
                                            <label class="wpallexport-xml-element">Variation Description</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_variation_description">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_variation_description">
                                            <input type="hidden" name="cc_name[]" value="Variation Description">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="81">
                                            <label class="wpallexport-xml-element">WC Rating Count</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wc_rating_count">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_wc_rating_count">
                                            <input type="hidden" name="cc_name[]" value="WC Rating Count">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="82">
                                            <label class="wpallexport-xml-element">WC Review Count</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wc_review_count">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_wc_review_count">
                                            <input type="hidden" name="cc_name[]" value="WC Review Count">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other wp_all_export_auto_generate ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="83">
                                            <label class="wpallexport-xml-element">WC Average Rating</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_wc_average_rating">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_wc_average_rating">
                                            <input type="hidden" name="cc_name[]" value="WC Average Rating">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="84">
                                            <label class="wpallexport-xml-element">Min Variation Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_variation_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_variation_price">
                                            <input type="hidden" name="cc_name[]" value="Min Variation Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="85">
                                            <label class="wpallexport-xml-element">Max Variation Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_variation_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_variation_price">
                                            <input type="hidden" name="cc_name[]" value="Max Variation Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="86">
                                            <label class="wpallexport-xml-element">Min Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Min Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="87">
                                            <label class="wpallexport-xml-element">Max Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Max Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="88">
                                            <label class="wpallexport-xml-element">Min Variation Regular Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_variation_regular_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_variation_regular_price">
                                            <input type="hidden" name="cc_name[]" value="Min Variation Regular Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="89">
                                            <label class="wpallexport-xml-element">Max Variation Regular Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_variation_regular_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_variation_regular_price">
                                            <input type="hidden" name="cc_name[]" value="Max Variation Regular Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="90">
                                            <label class="wpallexport-xml-element">Min Regular Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_regular_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_regular_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Min Regular Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="91">
                                            <label class="wpallexport-xml-element">Max Regular Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_regular_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_regular_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Max Regular Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="92">
                                            <label class="wpallexport-xml-element">Min Variation Sale Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_variation_sale_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_variation_sale_price">
                                            <input type="hidden" name="cc_name[]" value="Min Variation Sale Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="93">
                                            <label class="wpallexport-xml-element">Max Variation Sale Price</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_variation_sale_price">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_variation_sale_price">
                                            <input type="hidden" name="cc_name[]" value="Max Variation Sale Price">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="94">
                                            <label class="wpallexport-xml-element">Min Sale Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_min_sale_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_min_sale_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Min Sale Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                    <li class="pmxe_other  ui-draggable ui-draggable-handle">
                                        <div class="custom_column" rel="95">
                                            <label class="wpallexport-xml-element">Max Sale Price Variation ID</label>
                                            <input type="hidden" name="ids[]" value="1">
                                            <input type="hidden" name="cc_label[]" value="_max_sale_price_variation_id">
                                            <input type="hidden" name="cc_php[]" value="0">
                                            <input type="hidden" name="cc_code[]" value="">
                                            <input type="hidden" name="cc_sql[]" value="0">
                                            <input type="hidden" name="cc_options[]" value="0">
                                            <input type="hidden" name="cc_type[]" value="woo">
                                            <input type="hidden" name="cc_value[]" value="_max_sale_price_variation_id">
                                            <input type="hidden" name="cc_name[]" value="Max Sale Price Variation ID">
                                            <input type="hidden" name="cc_settings[]" value="0">
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </ul>

                    </div>

                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
</div>