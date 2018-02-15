<?php

namespace Wpae\App\Service\Pro\VariationOptions;


use Wpae\App\Service\VariationOptions\VariationOptionsInterface;
use Wpae\App\Service\VariationOptions\VariationOptions as BasicVariationOptions;

class VariationOptions extends BasicVariationOptions implements VariationOptionsInterface
{
    public function preprocessPost(\WP_Post $entry)
    {
        $productVariationMode = \XmlExportEngine::getProductVariationMode();

        if (!$this->shouldTitleBeProcessed($productVariationMode)) {
            return $entry;
        }

        if($entry->post_type != 'product_variation') {
            return $entry;
        }

        if ($entry->post_type == 'product_variation') {
                $entryId = $entry->ID;
                $entryTitle = $entry->post_title;
                $entryStatus = $entry->post_status;
                $entryOrder = $entry->menu_order;
                $parentId = $entry->post_parent;
                $parent = get_post($parentId);
                if ( ! empty($parent) ){
                    $parent->originalPost = clone $entry;
                    $entry = $parent;
                    $entry->ID = $entryId;
                    $entry->post_status = $entryStatus;
                    $entry->menu_order = $entryOrder;
                    $entry->post_parent = $parentId;
                    if (\XmlExportEngine::getProductVariationTitleMode() == \XmlExportEngine::VARIATION_USE_DEFAULT_TITLE) {
                        $entry->post_title = $entryTitle;
                    }
                    $entry->post_type = 'product_variation';
                }
            }

        return $entry;
    }

    public function getQueryWhere($wpdb, $where, $join, $closeBracket = false)
    {
        if (\XmlExportEngine::getProductVariationMode() == \XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT) {
            return " AND ($wpdb->posts.post_type = 'product') ";
        } else if (\XmlExportEngine::getProductVariationMode() == \XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION) {

            return " AND $wpdb->posts.ID NOT IN (
                SELECT DISTINCT $wpdb->posts.post_parent
                            FROM $wpdb->posts
                            WHERE $wpdb->posts.post_type = 'product_variation'
                        ) AND $wpdb->posts.ID NOT IN (SELECT o.ID FROM $wpdb->posts o
                            LEFT OUTER JOIN $wpdb->posts r
                            ON o.post_parent = r.ID
                            WHERE r.post_status = 'trash' AND o.post_type = 'product_variation') 
                            OR ($wpdb->posts.post_type = 'product_variation' AND $wpdb->posts.post_parent IN (
                SELECT DISTINCT $wpdb->posts.ID
                            FROM $wpdb->posts $join
                            WHERE $where
                        ))";
        } else {
            return $this->defaultQuery($wpdb, $where, $join, $closeBracket);
        }
    }

    /**
     * @param $productVariationMode
     * @return bool
     */
    private function shouldTitleBeProcessed($productVariationMode)
    {
        return $productVariationMode != \XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT ||
        $productVariationMode == \XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION;
    }
}