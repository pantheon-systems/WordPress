<?php

namespace Wpae\App\Service\VariationOptions;


class VariationOptions implements VariationOptionsInterface
{
    public function getQueryWhere($wpdb, $where, $join, $closeBracket = false)
    {
        return $this->defaultQuery($wpdb, $where, $join, $closeBracket);
    }

    public function preProcessPost(\WP_Post $entry)
    {
        return $entry;
    }

    /**
     * @param $wpdb
     * @param $where
     * @param $join
     * @param $closeBracket
     * @return string
     *
     * TODO: Remove $closeBracket flag
     */
    protected function defaultQuery($wpdb, $where, $join, $closeBracket)
    {
        $langQuery = '';

        if($this->isLanguageFilterEnabled()) {
            $langQuery .= " AND t.language_code = '".\XmlExportEngine::$exportOptions['wpml_lang']."' ";
        }

        if($closeBracket) {
            return " AND $wpdb->posts.post_type = 'product' " . $langQuery . " AND $wpdb->posts.ID NOT IN (SELECT o.ID FROM $wpdb->posts o
                            LEFT OUTER JOIN $wpdb->posts r
                            ON o.post_parent = r.ID
                            WHERE r.post_status = 'trash' AND o.post_type = 'product_variation')) 
                            OR ($wpdb->posts.post_type = 'product_variation'  AND $wpdb->posts.post_status <> 'trash' AND $wpdb->posts.post_parent IN (
                            SELECT DISTINCT $wpdb->posts.ID
                            FROM $wpdb->posts $join
                            WHERE $where
                        ))";
        } else {
            return " AND $wpdb->posts.post_type = 'product' " . $langQuery . " AND $wpdb->posts.ID NOT IN (SELECT o.ID FROM $wpdb->posts o
                            LEFT OUTER JOIN $wpdb->posts r
                            ON o.post_parent = r.ID
                            WHERE r.post_status = 'trash' AND o.post_type = 'product_variation') 
                            OR ($wpdb->posts.post_type = 'product_variation' AND $wpdb->posts.post_status <> 'trash' AND $wpdb->posts.post_parent IN (
                            SELECT DISTINCT $wpdb->posts.ID
                            FROM $wpdb->posts $join
                            WHERE $where
                        ))";
        }

    }

    /**
     * @return bool
     */
    private function isLanguageFilterEnabled()
    {
        return class_exists('SitePress') &&
            !empty(\XmlExportEngine::$exportOptions['wpml_lang']) &&
            (\XmlExportEngine::$exportOptions['wpml_lang'] !== 'all');
    }
}