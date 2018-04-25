<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringUsers
 * @package Wpae\Pro\Filtering
 */
class FilteringUsers extends FilteringBase
{
    /**
     * @return bool
     */
    public function parse(){
        if ( $this->isFilteringAllowed()){
            $this->checkNewStuff();

            // No Filtering Rules defined
            if ( empty($this->filterRules)) return FALSE;

            $this->queryWhere = $this->isExportNewStuff() ? $this->queryWhere . " AND (" : " AND (";

            // Apply Filtering Rules
            foreach ($this->filterRules as $rule) {
                if ( is_null($rule->parent_id) ) {
                    $this->parse_single_rule($rule);
                }
            }
            if ($this->meta_query || $this->tax_query) {
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->users}.ID";
            }
            else {
                $this->queryWhere .= ")";
            }
        }
    }

    /**
     *
     */
    public function checkNewStuff(){
        //If re-run, this export will only include records that have not been previously exported.
        if ($this->isExportNewStuff()){
            $postList = new \PMXE_Post_List();
            $this->queryWhere = " AND ({$this->wpdb->users}.ID NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }
    }

    /**
     * @param $rule
     */
    public function parse_single_rule($rule){
        apply_filters('wp_all_export_single_filter_rule', $rule);
        switch ($rule->element) {
            case 'ID':
                $this->queryWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule, true);
                break;
            case 'user_role':
                $cap_key = $this->wpdb->prefix . 'capabilities';
                $this->queryJoin[] = " INNER JOIN {$this->wpdb->usermeta} ON ( {$this->wpdb->users}.ID = {$this->wpdb->usermeta}.user_id ) ";
                $this->queryWhere .= "{$this->wpdb->usermeta}.meta_key = '$cap_key' AND {$this->wpdb->usermeta}.meta_value " . $this->parse_condition($rule);
                break;
            case 'user_registered':
                $this->parse_date_field( $rule );
                $this->queryWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule);
                break;
            case 'user_status':
            case 'display_name':
            case 'user_login':
            case 'user_nicename':
            case 'user_email':
            case 'user_url':
                $this->queryWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule);
                break;
            case 'blog_id':

                break;
            default:
                if (strpos($rule->element, "cf_") === 0)
                {
                    $this->meta_query = true;
                    $meta_key = str_replace("cf_", "", $rule->element);

                    if ($rule->condition == 'is_empty'){
                        $table_alias = (count($this->queryJoin) > 0) ? 'meta' . count($this->queryJoin) : 'meta';
                        $this->queryJoin[] = " LEFT JOIN {$this->wpdb->usermeta} AS $table_alias ON ($table_alias.user_id = {$this->wpdb->users}.ID AND $table_alias.meta_key = '$meta_key') ";
                        $this->queryWhere .= "$table_alias.umeta_id " . $this->parse_condition($rule);
                    }
                    else{
                        $table_alias = (count($this->queryJoin) > 0) ? 'meta' . count($this->queryJoin) : 'meta';
                        $this->queryJoin[] = " INNER JOIN {$this->wpdb->usermeta} AS $table_alias ON ( {$this->wpdb->users}.ID = $table_alias.user_id ) ";
                        $this->queryWhere .= "$table_alias.meta_key = '$meta_key' AND $table_alias.meta_value " . $this->parse_condition($rule, false, $table_alias);
                    }
                }
                break;
        }
        $this->recursion_parse_query($rule);
    }
}