<?php

namespace Wpae\Pro\Filtering;


/**
 * Class FilteringProducts
 * @package Wpae\Filtering
 */
use Wpae\App\Service\VariationOptions\VariationOptionsFactory;

/**
 * Class FilteringProducts
 * @package Wpae\Pro\Filtering
 */
class FilteringProducts extends FilteringCPT
{
    /**
     *
     */
    public function parse(){
        if ( $this->isFilteringAllowed()){
          $this->checkNewStuff();

          // No Filtering Rules defined
          if ( empty($this->filterRules)) {
              return $this->noRulesDefined();
          }

          //$this->queryWhere = $this->isExportNewStuff() ? $this->queryWhere . " AND (" : " AND (";
          $this->queryWhere = ($this->isExportNewStuff() || $this->isExportModifiedStuff()) ? $this->queryWhere . " AND (" : " AND (";

          $this->applyRules();

          if ($this->meta_query || $this->tax_query) {
            $this->queryWhere .= " GROUP BY {$this->wpdb->posts}.ID";
          }
        }
    }

    /**
     *
     */
    private function noRulesDefined(){

        $tmp_queryWhere = $this->queryWhere;
        $tmp_queryJoin  = $this->queryJoin;

        $this->queryJoin = array();

        $this->queryWhere = " {$this->wpdb->posts}.post_type = 'product' AND (({$this->wpdb->posts}.post_status <> 'trash' AND {$this->wpdb->posts}.post_status <> 'auto-draft'))";

        if ( $this->isExportNewStuff() ) {
            $postList = new \PMXE_Post_List();
            $this->queryWhere .= " AND ({$this->wpdb->posts}.ID NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }
        if ($this->isExportModifiedStuff() && ! empty(\XmlExportEngine::$exportRecord->registered_on)){
            $this->queryWhere .= " AND {$this->wpdb->posts}.post_modified > '" . \XmlExportEngine::$exportRecord->registered_on . "' ";
        }

        $where = $this->queryWhere;
        $join  = implode( ' ', array_unique( $this->queryJoin ) );

        if ($this->isLanguageFilterEnabled()){
            $where .= " AND t.language_code = '".\XmlExportEngine::$exportOptions['wpml_lang']."' ";
        }
        $this->queryWhere = $tmp_queryWhere;
        $this->queryJoin  = $tmp_queryJoin;

        $variationOptionsFactory = new VariationOptionsFactory();
        $variationOptions = $variationOptionsFactory->createVariationOptions(PMXE_EDITION);

        $this->queryWhere .= $variationOptions->getQueryWhere($this->wpdb, $where, $join, false);
    }

    /**
     *
     */
    private function applyRules(){

        // Apply Filtering Rules
        foreach ($this->filterRules as $rule) {
            if ( is_null($rule->parent_id) ) {
                $this->parse_single_rule($rule);
            }
        }

        $tmp_queryWhere = $this->queryWhere;
        $tmp_queryJoin  = $this->queryJoin;

        $this->queryJoin = array();

        $this->queryWhere = " {$this->wpdb->posts}.post_type = 'product' AND (({$this->wpdb->posts}.post_status <> 'trash' AND {$this->wpdb->posts}.post_status <> 'auto-draft')) AND (";
        foreach ($this->filterRules as $rule) {
            if ( is_null($rule->parent_id) ) {
                $this->parse_single_rule($rule);
            }
        }
        $this->queryWhere .= ")";

        if ( $this->isExportNewStuff() ) {
            $postList = new \PMXE_Post_List();
            $this->queryWhere .= " AND ({$this->wpdb->posts}.ID NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }

        if ($this->isExportModifiedStuff() && ! empty(\XmlExportEngine::$exportRecord->registered_on)){
            $this->queryWhere .= " AND {$this->wpdb->posts}.post_modified > '" . \XmlExportEngine::$exportRecord->registered_on . "' ";
        }

        $where = $this->queryWhere;
        $join  = implode( ' ', array_unique( $this->queryJoin ) );
        if ($this->isLanguageFilterEnabled()){
            $where .= " AND t.language_code = '".\XmlExportEngine::$exportOptions['wpml_lang']."' ";
        }
        $this->queryWhere = $tmp_queryWhere;
        $this->queryJoin  = $tmp_queryJoin;

        $vatiationOptionsFactory = new VariationOptionsFactory();
        $variationOptions = $vatiationOptionsFactory->createVariationOptions(PMXE_EDITION);

        $this->queryWhere .= ") " . $variationOptions->getQueryWhere($this->wpdb, $where, $join);

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