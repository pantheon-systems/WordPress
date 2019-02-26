<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.31 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function addProductById() {
	global $ProdId, $wpdb, $Action, $xml;
	
	$filters = array( 
		'post_type' => array('product', 'product_variation'), 
		'post__in' => array($ProdId),
		'orderby' =>'modified',
		'order' => 'DESC'
	);
    $loop = new WP_Query( $filters );
    while ( $loop->have_posts() ) {
		$loop->the_post();
		global $product;
		
		//var_dump($product);
		$postType = get_post_type($ProdId);
		$product = new WC_Product($ProdId);  
		$product_xml = $xml->AddChild('Product');
		$product_xml->AddAttribute('Id', $product->id);
		$product_xml->AddAttribute('LastUpdated', $product->get_post_data()->post_modified_gmt);
		$product_xml->AddAttribute('IsDeleted', 'false');
		$product_xml->Code = getProductCode($product);
		if($postType == 'product_variation')
		{
			$prod = new WC_Product($product->get_parent());	
			$product_xml->Name = $prod->get_title();			
			$prod_meta = get_post_meta($product->id);
			$sanitized_values = array();
			foreach($prod_meta as $custom_meta => $custom_meta_value){
				if($custom_meta[0] != '_')
				{
					$custom_meta_value = implode(',',$custom_meta_value);
					$sanitized_values[] = xml_entities($custom_meta_value);
				}	
			}	
			$product_xml->Name .= ' ('.implode(',', $sanitized_values).')';
			$_variable_description = get_post_meta( $product->id , '_variation_description', true );
			$product_xml->Description = xml_entities(strip_tags($_variable_description));		
		}
		else
		{
			$product_xml->Name = xml_entities($product->get_title());
			$product_xml->Description = xml_entities(strip_tags($product->get_post_data()->post_excerpt));				
		}	

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
