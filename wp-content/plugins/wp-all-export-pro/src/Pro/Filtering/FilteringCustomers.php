<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringCustomers
 * @package Wpae\Pro\Filtering
 */
class FilteringCustomers extends FilteringUsers
{
    /**
     * @return bool
     */
    public function parse(){
        if ( $this->isFilteringAllowed()){
            $this->checkNewStuff();

            // No Filtering Rules defined
            if ( empty($this->filterRules)) {
                $in_users = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
                $this->queryWhere .= " AND {$this->wpdb->users}.ID IN (" . $in_users . ") GROUP BY {$this->wpdb->users}.ID";
                return FALSE;
            }

            $this->queryWhere = $this->isExportNewStuff() ? $this->queryWhere . " AND (" : " AND (";

            // Apply Filtering Rules
            foreach ($this->filterRules as $rule) {
                if ( is_null($rule->parent_id) ) {
                    $this->parse_single_rule($rule);
                }
            }
            $in_users = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
            $this->queryWhere .= " AND {$this->wpdb->users}.ID IN (" . $in_users . ")";
            if ($this->meta_query || $this->tax_query) {
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->users}.ID";
            }
            else {
                $this->queryWhere .= ")";
            }
        }
    }
}