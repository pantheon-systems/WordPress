<?php

function wp_all_export_get_cpt_name($cpt = array(), $count = 2, $post = array())
{
	$cptName = '';
	if ( ! empty($cpt))
	{
		if (in_array('users', $cpt))
		{
			$cptName = ($count > 1) ? __('Users', 'wp_all_export_plugin') : __('User', 'wp_all_export_plugin');
		}
		elseif (in_array('shop_customer', $cpt))
		{
			$cptName = ($count > 1) ? __('Customers', 'wp_all_export_plugin') : __('Customer', 'wp_all_export_plugin');
		}
		elseif (in_array('comments', $cpt))
		{
			$cptName = ($count > 1) ? __('Comments', 'wp_all_export_plugin') : __('Comment', 'wp_all_export_plugin');
		}
        elseif (in_array('taxonomies', $cpt))
        {
            if (!empty($post['taxonomy_to_export'])){
                $tx = get_taxonomy( $post['taxonomy_to_export'] );
                $cptName = ($count > 1) ? $tx->labels->name : $tx->labels->singular_name;
            }
            else{
                $cptName = ($count > 1) ? __('Taxonomy Terms', 'wp_all_export_plugin') : __('Taxonomy Term', 'wp_all_export_plugin');
            }
        }
        else
		{
			if (count($cpt) === 1 and in_array('product_variation', $cpt) and class_exists('WooCommerce')){
				$cptName = ($count > 1) ? 'Variations' : 'Variation';
			}
			else
			{
				$post_type_details = get_post_type_object( $cpt[0] );				
				if ($post_type_details)
				{
					$cptName = ($count > 1) ? $post_type_details->labels->name : $post_type_details->labels->singular_name;
				}				
			}			
		}
	}
	if (empty($cptName))
	{
		$cptName = ($count > 1) ? __('Records', 'wp_all_export_plugin') : __('Record', 'wp_all_export_plugin');
	}

	return $cptName;
}