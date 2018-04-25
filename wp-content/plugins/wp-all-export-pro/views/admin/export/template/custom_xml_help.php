<div id="wp-all-export-custom-xml-help-inner" class="wp_all_export_custom_xml_help">

    <p style="margin-top:5px;"><?php _e('The custom XML editor makes it easy to create an XML file with the exact structure you need. The syntax is simple and straightforward, yet powerful enough to allow you to pass your data through custom PHP functions.', 'wp_all_export_plugin'); ?></p>

    <h3 id="wpae_help_custom_xml_editor_tab"><span>+</span>&nbsp;<?php _e('Custom XML Editor', 'wp_all_export_plugin'); ?></h3>

    <div rel="wpae_help_custom_xml_editor_tab" class="wp_all_export_help_tab">
        <p><?php _e('The custom XML editor is a template for your custom XML feed. Everything between the <span class="wp_all_export_code"><span class="wp_all_export_code_comment">&lt;!-- BEGIN LOOP --&gt;</span> and <span class="wp_all_export_code_comment">&lt;!-- END LOOP --&gt;</span></span> tags will be repeated for each exported post.','wp_all_export_plugin');?></p>
        <p><?php _e('You can drag and drop elements from Available Data on the right into the editor on the left. You can also manually enter data into the export template.','wp_all_export_plugin');?></p>
        <p><?php _e('For example, to add the post title to your export, you can either drag the title element into the editor, or you can manually edit the export template in editor to add it like this: <span class="wp_all_export_code"><span class="wp_all_export_code_tag">&lt;my_custom_title&gt;<span class="wp_all_export_code_text">{Title}</span>&lt;/my_custom_title&gt;</span></span>', 'wp_all_export_plugin');?></p>
    </div>

    <h3 id="wpae_help_php_functions_tab"><span>+</span>&nbsp;<?php _e('PHP Functions', 'wp_all_export_plugin'); ?></h3>

    <div rel="wpae_help_php_functions_tab" class="wp_all_export_help_tab">
        <p><?php _e('To add a custom PHP function to your XML template wrap it in brackets: <span class="wp_all_export_code"><span class="wp_all_export_code_text">[my_function({Content})]','wp_all_export_plugin');?></span></span></p>
        <p><?php _e('You can also use native PHP functions: <span class="wp_all_export_code"><span class="wp_all_export_code_text">[str_replace(",","",{Price})]','wp_all_export_plugin');?></span></span></p>
        <p><?php _e('Whatever your function returns will appear in your exported XML file. You can pass as many elements as you like to your function so that they can be combined and processed in any way.','wp_all_export_plugin');?></p>
    </div>

    <h3 id="wpae_help_repeating_fields_tab"><span>+</span>&nbsp;<?php _e('Repeating Fields and Arrays', 'wp_all_export_plugin'); ?></h3>

    <div rel="wpae_help_repeating_fields_tab" class="wp_all_export_help_tab">
        <p><?php _e('Some fields, like images, have multiple values per post. WP All Export turns these fields into indexed arrays. Whenever WP All Export encounters an indexed array in an XML element it will repeat that element once for every value in the array.', 'wp_all_export_plugin');?></p>
        <p><?php _e('For example, let\'s assume a post as two images attached to it - image1.jpg and image2.jpg - and we want to have one XML element for every image URL. Here\'s what our XML template will look like:', 'wp_all_export_plugin');?></p>

        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag">&lt;images&gt;</p>
                <p class="wp_all_export_code_tag lv2">&lt;image_url&gt;<span class="wp_all_export_code_text">{Image URL}</span>&lt;/image_url&gt;</p>
            <p class="wp_all_export_code_tag">&lt;/images&gt;</p>
        </div>

        <p><?php _e('And here\'s how our exported XML file will look:', 'wp_all_export_plugin');?></p>

        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag">&lt;images&gt;</p>
                <p class="wp_all_export_code_tag lv2">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/image1.jpg</span>&lt;/image_url&gt;</p>
                <p class="wp_all_export_code_tag lv2">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/image2.jpg</span>&lt;/image_url&gt;</p>
            <p class="wp_all_export_code_tag">&lt;/images&gt;</p>
        </div>
        <p><?php _e('WP All Export will do this with all indexed arrays that it comes across. So if you have a function that returns an indexed array, that XML element will be repeated for each value. Likewise, you can take a field like {Image URL} and turn it into a string, like this:', 'wp_all_export_plugin');?></p>
        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag">&lt;images&gt;[implode(&quot;|&quot;,{Image Title})]&lt;/images&gt;</p>
        </div>
        <p><?php _e("And you'll just get one XML element with all of the values, like this:", 'wp_all_export_plugin');?></p>
        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag">&lt;images&gt;Image 1|Image 2&lt;/images&gt;</p>
        </div>
    </div>

    <h3 id="wpae_help_example_template_tab"><span>+</span>&nbsp;<?php _e('Example Template', 'wp_all_export_plugin');?></h3>

    <div rel="wpae_help_example_template_tab" class="wp_all_export_help_tab">

        <p><?php _e('Let\'s say we want to make an XML feed of our WooCommerce products with these requirements:', 'wp_all_export_plugin'); ?></p>
        <ul>
            <li><?php _e('Site name below the header, before the <span class="wp_all_export_code"><span class="wp_all_export_code_tag">&lt;products&gt;</span></span> element', 'wp_all_export_plugin');?></li>
            <li><?php _e('Product SKU', 'wp_all_export_plugin');?></li>
            <li><?php _e('Product Title', 'wp_all_export_plugin');?></li>
            <li><?php _e('Product Price (processed via a PHP function so that they end in .99)', 'wp_all_export_plugin');?></li>
            <li><?php _e('Product image URLs wrapped in an <span class="wp_all_export_code"><span class="wp_all_export_code_tag">&lt;images&gt;</span></span> element', 'wp_all_export_plugin');?></li>
        </ul>
        <p><?php _e('Here\'s what our XML template will look like in the editor:', 'wp_all_export_plugin'); ?></p>
        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag cm-s-default"><span class="cm-meta">&lt;?xml version="1.0" encoding="UTF-8"?&gt;</span></p>
            <p class="wp_all_export_code_tag">&lt;site_name&gt;<span class="wp_all_export_code_text">My Soda Store</span>&lt;/site_name&gt;</p>
                <p class="wp_all_export_code_tag">&lt;products&gt;</p>
                <p class="wp_all_export_code_comment lv1">&lt;!-- BEGIN LOOP --&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;product&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;sku&gt;<span class="wp_all_export_code_text">{SKU}</span>&lt;/sku&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;title&gt;<span class="wp_all_export_code_text">{Title}</span>&lt;/title&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;price&gt;<span class="wp_all_export_code_text">[my_price_function({Price})]</span>&lt;/price&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;images&gt;</p>
                        <p class="wp_all_export_code_tag lv3">&lt;image_url&gt;<span class="wp_all_export_code_text">{Image URL}</span>&lt;/image_url&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;/images&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;/product&gt;</p>
                <p class="wp_all_export_code_comment lv1">&lt;!-- END LOOP --&gt;</p>
            <p class="wp_all_export_code_tag">&lt;/products&gt;</p>
        </div>

        <p><?php _e('Then in the Function Editor we\'d define my_price_function() like so:', 'wp_all_export_plugin');?></p>

        <p class="cm-s-default code-block">
            <span class="cm-keyword">function</span> <span class="cm-def">my_price_function</span>( <span class="cm-variable-2">$price</span> ) {<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cm-keyword">return</span> <span class="cm-builtin">number_format</span>(<span class="cm-builtin">round</span>(<span class="cm-variable-2">$price</span>,0)-0.01,2);<br/>
            }
        </p>

        <p><?php _e('If we had two products, each with two images, here\'s what our XML file would look like:', 'wp_all_export_plugin');?></p>

        <div class="wp_all_export_code code-block">
            <p class="wp_all_export_code_tag cm-s-default"><span class="cm-meta">&lt;?xml version="1.0" encoding="UTF-8"?&gt;</span></p>
            <p class="wp_all_export_code_tag">&lt;site_name&gt;<span class="wp_all_export_code_text">My Soda Store</span>&lt;/site_name&gt;</p>
            <p class="wp_all_export_code_tag">&lt;products&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;product&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;sku&gt;<span class="wp_all_export_code_text">pepsi_cola</span>&lt;/sku&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;title&gt;<span class="wp_all_export_code_text">Pepsi</span>&lt;/title&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;price&gt;<span class="wp_all_export_code_text">1.99</span>&lt;/price&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;images&gt;</p>
                        <p class="wp_all_export_code_tag lv3">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/pepsi_1.jpg</span>&lt;/image_url&gt;</p>
                        <p class="wp_all_export_code_tag lv3">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/pepsi_2.jpg</span>&lt;/image_url&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;/images&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;/product&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;product&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;sku&gt;<span class="wp_all_export_code_text">coca_cola</span>&lt;/sku&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;title&gt;<span class="wp_all_export_code_text">Coke</span>&lt;/title&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;price&gt;<span class="wp_all_export_code_text">1.99</span>&lt;/price&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;images&gt;</p>
                        <p class="wp_all_export_code_tag lv3">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/coke_1.jpg</span>&lt;/image_url&gt;</p>
                        <p class="wp_all_export_code_tag lv3">&lt;image_url&gt;<span class="wp_all_export_code_text">http://example.com/coke_2.jpg</span>&lt;/image_url&gt;</p>
                    <p class="wp_all_export_code_tag lv2">&lt;/images&gt;</p>
                <p class="wp_all_export_code_tag lv1">&lt;/product&gt;</p>
            <p class="wp_all_export_code_tag">&lt;/products&gt;</p>
        </div>
    </div>
</div>