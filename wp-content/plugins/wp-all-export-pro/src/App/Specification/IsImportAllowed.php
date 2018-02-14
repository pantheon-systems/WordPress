<?php

namespace Wpae\App\Specification;


class IsImportAllowed
{
    public function isSatisfied($item)
    {
        $is_re_import_allowed = true;
        if ( ! empty($item['options']['ids']) )
        {
            if (in_array('shop_order', $item['options']['cpt']) and class_exists('WooCommerce')) {
                $required_fields = array('woo_order' => 'id');
            }
            else {
                $required_fields = array('id' => 'id');
            }
            // re-import products
            if ((in_array('product', $item['options']['cpt']) or $item['options']['export_type'] == 'advanced') and class_exists('WooCommerce') and (empty($item['options']['wp_query_selector']) or $item['options']['wp_query_selector'] == 'wp_query')) {
                $required_fields['woo']  = '_sku';
                $required_fields['cats'] = 'product_type';
                $required_fields['parent'] = 'parent';
            }
            if ((in_array('users', $item['options']['cpt']) or $item['options']['export_type'] == 'advanced') and (!empty($item['options']['wp_query_selector']) and $item['options']['wp_query_selector'] == 'wp_user_query')) {
                $required_fields['user_email']  = 'user_email';
                $required_fields['user_login']  = 'user_login';
            }
            if ($item['options']['export_type'] == 'advanced' and (empty($item['options']['wp_query_selector']) or $item['options']['wp_query_selector'] == 'wp_query')){
                $required_fields['post_type'] = 'post_type';
            }
            $defined_fields = array();
            foreach ($item['options']['ids'] as $ID => $value)
            {
                foreach ($required_fields as $type => $field)
                {
                    if (strtolower($item['options']['cc_type'][$ID]) == $type && strtolower($item['options']['cc_label'][$ID]) == strtolower($field)){
                        $defined_fields[] = $field;
                    }
                }
            }

            foreach ($required_fields as $type => $field) {
                if ( ! in_array($field, $defined_fields) ){
                    $is_re_import_allowed = false;
                    break;
                }
            }
        }

        return $is_re_import_allowed;
    }
}