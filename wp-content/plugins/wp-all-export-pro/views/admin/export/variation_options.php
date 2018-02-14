<?php
/** @var $post */
/** @var string $random */
$random = uniqid();
?>
<div class="product_variations">
    <h4><?php _e('Product Variations', 'wp_all_export_plugin'); ?>
        <a href="#help" class="wpallexport-help"
         style="position: relative; top: 0px;"
         title="<?php _e('WooCommerce stores each product variation as a separate product in the database, along with a parent product to tie all of the variations together.<br/><br/>If the product title is \'T-Shirt\', then the parent product will be titled \'T-Shirt\', and in the database each size/color combination will be a separate product with a title like \'Variation #23 of T-Shirt\'.', 'wp_all_export_plugin'); ?>">?</a></h4>
    <div class="input" style="display: inline-block; width: 100%;">
        <div>
            <label>
                <input type="radio" class="export_variations <?php if (PMXE_EDITION != 'paid') {
                    echo "variations_disabled";
                } ?>"
                       value="<?php echo XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION; ?>" <?php if ($post['export_variations'] == XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION && PMXE_EDITION == 'paid') { ?> checked="checked"  <?php } ?>
                       name="<?php echo $random?>_export_variations"/><?php _e("Export product variations and their parent products", 'wp_all_export_plugin'); ?>
            </label>
            <div class="sub-options sub-options-<?php echo XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION;?>">
                <label>
                    <input type="radio"
                           name="<?php echo $random; ?>_export_variations_title_1"
                           value="<?php echo XmlExportEngine::VARIATION_USE_PARENT_TITLE; ?>"
                        <?php if($post['export_variations_title'] == XmlExportEngine::VARIATION_USE_PARENT_TITLE) {?>
                            checked="checked"
                        <?php }?>
                           class="export_variations_title">
                    <?php _e("Product variations use the parent product title", 'wp_all_export_plugin');?>
                </label>
                <div class="clear"></div>
                <label>
                    <input type="radio"
                           name="<?php echo $random; ?>_export_variations_title_1"
                           value="<?php echo XmlExportEngine::VARIATION_USE_DEFAULT_TITLE; ?>"
                        <?php if($post['export_variations_title'] == XmlExportEngine::VARIATION_USE_DEFAULT_TITLE) { ?>
                            checked="checked"
                        <?php } ?>
                           class="export_variations_title">
                    <?php _e("Product variations use the default variation product title", 'wp_all_export_plugin'); ?>
                </label>
            </div>
        </div>
        <div class="clear"></div>
        <div>
            <label>
                <input type="radio" class="export_variations"
                          value="<?php echo XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION; ?>" <?php if ($post['export_variations'] == XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION || PMXE_EDITION == 'free') { ?> checked="checked" <?php } ?>
                          name="<?php echo $random; ?>_export_variations"/><?php _e("Only export product variations", 'wp_all_export_plugin'); ?>
            </label>
            <div class="sub-options sub-options-<?php echo XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION; ?>">
                <label>
                    <input type="radio"
                              name="<?php echo $random; ?>_export_variations_title_2"
                              value="<?php echo XmlExportEngine::VARIATION_USE_PARENT_TITLE; ?>"
                              <?php if($post['export_variations_title'] == XmlExportEngine::VARIATION_USE_PARENT_TITLE) {?>
                                  checked="checked"
                              <?php }?>
                              class="export_variations_title">
                    <?php _e("Product variations use the parent product title", 'wp_all_export_plugin'); ?>
                </label>
                <div class="clear"></div>
                <label>
                    <input type="radio"
                              name="<?php echo $random; ?>_export_variations_title_2"
                              value="<?php echo XmlExportEngine::VARIATION_USE_DEFAULT_TITLE; ?>"
                              <?php if($post['export_variations_title'] == XmlExportEngine::VARIATION_USE_DEFAULT_TITLE) {?>
                                  checked="checked"
                              <?php } ?>
                              class="export_variations_title">
                    <?php _e("Product variations use the default variation product title", 'wp_all_export_plugin'); ?>
                </label>
            </div>
        </div>
        <div class="clear"></div>
        <div>
            <label>
                <input type="radio" class="export_variations <?php if (PMXE_EDITION != 'paid') {
                    echo "variations_disabled";
                } ?>"
                          value="<?php echo XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT; ?>" <?php if ($post['export_variations'] == XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT && PMXE_EDITION == 'paid') { ?> checked="checked" <?php } ?>
                          name="<?php echo $random?>_export_variations"/><?php _e("Only export parent products", 'wp_all_export_plugin'); ?>
            </label>
        </div>

    </div>
</div>