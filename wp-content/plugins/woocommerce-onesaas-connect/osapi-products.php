<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function addProducts() {
	global $PageSize, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml;

	$filters = array( 
		'post_type' => 'product', 
		'posts_per_page' => $PageSize,
		'paged' => $Page+1,
		'orderby' =>'modified',
		'order' => 'DESC'
	);
    $loop = new WP_Query( $filters );

    while ( $loop->have_posts() ) {
		$loop->the_post();
		global $product;
		
		$lastModified = strtotime($product->get_post_data()->post_modified_gmt.'UTC');
		if ($lastModified<$LastUpdatedTime) {
			break;
		}
		$product_xml = $xml->AddChild('Product');
		$product_xml->AddAttribute('Id', $product->id);
		$product_xml->AddAttribute('LastUpdated', $product->get_post_data()->post_modified_gmt);
		$product_xml->AddAttribute('IsDeleted', 'false');
		$product_xml->Code = getProductCode($product);
		$product_xml->Name = xml_entities($product->get_title());
		$primary_supplier = woocommerce_get_product_terms($product->id, 'pa_supplier', 'names');
		$product_xml->PrimarySupplier = array_shift($primary_supplier);
		$primary_manufacturer = woocommerce_get_product_terms($product->id, 'pa_manufacturer', 'names');
		$product_xml->PrimaryManufacturer = array_shift($primary_manufacturer);
		$product_xml->Description = xml_entities(strip_tags($product->get_post_data()->post_excerpt));
		//$product_xml->IsActive = ($product->is_on_sale()=='1')?'true':'false';
		$product_xml->IsActive = 'true';
		$product_xml->Url = admin_url() . 'post.php?post=' . $product->id . '&action=edit';
		$product_xml->PublicUrl = $product->get_post_data()->guid;
		$product_xml->IsInventoried = ($product->managing_stock()=='1')?'true':'false';
		$product_xml->StockAtHand = $product->managing_stock()?$product->get_total_stock():null;
		$product_xml->SalePrice = $product->get_price();
		$weight = $product_xml->addChild('Weight', $product->get_weight());
		$weight->AddAttribute('unit', 'kg');
		// Dimensions
		$dimensions = $product->get_dimensions();
		if (($dimensions != null) && ($dimensions != '')) {
			$dimensions_array = explode( ' x ', $dimensions );
			if (($dimensions_array!=null) && (is_array($dimensions_array)) && (sizeof($dimensions_array)==3)) {
				$length = $product_xml->addChild('Length', $dimensions_array[0]);
				$width = $product_xml->addChild('Width', $dimensions_array[1]);
				$height = $product_xml->addChild('Height', substr($dimensions_array[2],0,strpos($dimensions_array[2],' ')));
				$unit = substr($dimensions_array[2],strpos($dimensions_array[2],' ')+1);
				$length->AddAttribute('unit', $unit);
				$width->AddAttribute('unit', $unit);
				$height->AddAttribute('unit', $unit);
			}
		}
		$product_xml->Tags = strip_tags($product->get_tags());
		$product_xml->Type = 'Product';
		$cats = get_the_terms( $product->id, 'product_cat' );
		if(!empty($cats))
		{
		$categories_xml = $product_xml->AddChild('Categories');
			foreach ($cats as $cat) {	
				$product_cat_id = $cat->term_id;
				$category_xml = $categories_xml->AddChild('Category');
				if($cat->parent == 0)
				{
					$category_xml->ID = $product_cat_id;
					$category_xml->Name = $cat->name;			
				}
				else
				{
					$category_xml->ID = $product_cat_id;
					$category_xml->ParentID = $cat->parent;
					$category_xml->Name = $cat->name;
				}
			}
		}
		if ($product->is_type('grouped')) {
			$product_xml->Type = 'Combo';
			$combo_items_xml = $product_xml->AddChild('ComboItems');
			foreach ($product->get_children() as $combo_item_id) {
				$combo_item_xml = $combo_items_xml->addChild('ComboItem');
				$combo_item_xml->ProductId = $combo_item_id;
				$combo_item_xml->Quantity = 1;
			}
		}
		
		if ($product->is_type('variable')) {
			$masterCode = getProductCode($product);	
			$variations = get_variations($product);
			foreach($variations as $variation_id) {			
				$variation_xml = $xml->AddChild('Product');
				$variation = $product->get_child($variation_id['variation_id']);
				$variationCode = getProductCode($variation);
				$variation_xml->AddAttribute('Id', $variation_id['variation_id']);
				$variation_xml->AddAttribute('LastUpdated', $variation->get_post_data()->post_modified_gmt);
				$variation_xml->AddAttribute('IsDeleted', 'false');	
				// Variations have the same sku as master.  Adding id to differentiate, Otherwise leave variation SKU
				if($masterCode !==  $variationCode)
				{
					$variation_xml->Code = $variationCode;
				}
				else
				{
					$variation_xml->Code = $variationCode . '-' . $variation_id['variation_id'];	
				}	
				$variation_xml->MasterCode = $masterCode;
				$variation_xml->Name = xml_entities($variation->get_title());
				$PrimarySupplier = woocommerce_get_product_terms($variation->id, 'pa_supplier', 'names');
				$variation_xml->PrimarySupplier = array_shift($PrimarySupplier);
				$PrimaryManufacturer = woocommerce_get_product_terms($variation->id, 'pa_manufacturer', 'names');
				$variation_xml->PrimaryManufacturer = array_shift($PrimaryManufacturer);
				$product_xml->Description = xml_entities(strip_tags($product->get_post_data()->post_excerpt));
				//$variation_xml->IsActive = ($variation->is_on_sale()=='1')?'true':'false';
				$variation_xml->IsActive = 'true';
				$variation_xml->Url = admin_url() . 'post.php?post=' . $product->id . '&action=edit';
				$variation_xml->PublicUrl = $variation->get_post_data()->guid;
				$variation_xml->IsInventoried = ($variation->managing_stock()=='1')?'true':'false';
				$variation_xml->StockAtHand = $variation->managing_stock()?$variation->get_total_stock():null;
				$variation_xml->SalePrice = $variation->get_price();//$variation['price_html'];
				$weight = $variation_xml->addChild('Weight', $variation->get_weight());
				$weight->AddAttribute('unit', 'kg');
				// Dimensions
				$dimensions = $variation->get_dimensions();
				if (($dimensions != null) && ($dimensions != '')) {
					$dimensions_array = explode( ' x ', $dimensions );
					if (($dimensions_array!=null) && (is_array($dimensions_array)) && (sizeof($dimensions_array)==3)) {
						$length = $variation_xml->addChild('Length', $dimensions_array[0]);
						$width = $variation_xml->addChild('Width', $dimensions_array[1]);
						$height = $variation_xml->addChild('Height', substr($dimensions_array[2],0,strpos($dimensions_array[2],' ')));
						$unit = substr($dimensions_array[2],strpos($dimensions_array[2],' ')+1);
						$length->AddAttribute('unit', $unit);
						$width->AddAttribute('unit', $unit);
						$height->AddAttribute('unit', $unit);
					}
				}
				$variation_xml->Type = 'Product';
				$options = $variation_xml->AddChild('Options');
				$var_attributes = $variation->get_variation_attributes();
				if (($var_attributes != null) && is_array($var_attributes)) {
					foreach ($var_attributes as $attribute_name => $attribute_value) {
						$option = $options->AddChild('Option');
						$option->Name = htmlspecialchars(substr($attribute_name,10));
						$option->Value = htmlspecialchars($attribute_value);
					}
				}
				$cats = get_the_terms( $variation->id, 'product_cat' );				
				if(!empty($cats))
				{	
				$categories_xml = $variation_xml->AddChild('Categories');				
					foreach ($cats as $cat) {	
						$product_cat_id = $cat->term_id;
						$category_xml = $categories_xml->AddChild('Category');
						if($cat->parent == 0)
						{
							$category_xml->ID = $product_cat_id;
							$category_xml->Name = $cat->name;			
						}
						else
						{
							$category_xml->ID = $product_cat_id;
							$category_xml->ParentID = $cat->parent;
							$category_xml->Name = $cat->name;
						}
					}
				}
				//Add attribute custom fields
				$attributes = $variation->get_attributes();	
				if(!empty($attributes))
				{				
				$variation_xml = $variation_xml->AddChild('CustomFields');
				foreach ( $attributes as $attribute ) {	
					$pa_attr = explode("_", $attribute['name']);
					if ($pa_attr[0] == 'pa') {
						if($pa_attr[1] != 'manufacturer' && $pa_attr[1] != 'supplier')
							{
								$customfield_xml = $variation_xml->AddChild('CustomField',$variation->get_attribute( $attribute['name']));
								$customfield_xml->AddAttribute('Name',$pa_attr[1]);
							}
						}
					}
				}
				//Additional Custom Fields
				$prod_meta = get_post_meta($variation->id);
				foreach($prod_meta as $custom_meta => $custom_meta_value){
					 if($custom_meta[0] == '_')
					 {
						//Skip the meta_keys that start with _
						continue;
					 }
					 else
					{
						$custom_meta_value = implode(',',$custom_meta_value);
						$sanitized_value = xml_entities($custom_meta_value);
						$customfield_xml = $variation_xml->AddChild('CustomField', $sanitized_value);
						$customfield_xml->AddAttribute('Name',$custom_meta);
					 }
				}				
			}
		}
		//Add attribute custom fields
		$attributes = $product->get_attributes();
		if(!empty($attributes))
		{			
		$product_xml = $product_xml->AddChild('CustomFields');		
		foreach ( $attributes as $attribute ) {	
			$pa_attr = explode("_", $attribute['name']);
			if ($pa_attr[0] == 'pa') {
				if($pa_attr[1] != 'manufacturer' && $pa_attr[1] != 'supplier')
					{
						$customfield_xml = $product_xml->AddChild('CustomField',$product->get_attribute( $attribute['name']));
						$customfield_xml->AddAttribute('Name',$pa_attr[1]);
					}
				}
			}
		}
		//Additional Custom Fields
		$prod_meta = get_post_meta($product->id);
		foreach($prod_meta as $custom_meta => $custom_meta_value){
			 if($custom_meta[0] == '_')
			 {
				//Skip the meta_keys that start with _
				continue;
			 }
			 else
			{
				$custom_meta_value = implode(',',$custom_meta_value);
				$sanitized_value = xml_entities($custom_meta_value);
				$customfield_xml = $product_xml->AddChild('CustomField', $sanitized_value);
				$customfield_xml->AddAttribute('Name',$custom_meta);
			}
		}			
	}
    wp_reset_query(); 
}
?>
