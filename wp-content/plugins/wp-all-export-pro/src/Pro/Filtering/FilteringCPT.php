<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringCPT
 * @package Wpae\Pro\Filtering
 */
class FilteringCPT extends FilteringBase
{

    /**
     * @return bool
     */
    public function parse(){

        if ( $this->isFilteringAllowed()){

            $this->checkNewStuff();

            // No Filtering Rules defined
            if ( empty($this->filterRules)) return FALSE;

            $this->queryWhere = ($this->isExportNewStuff() || $this->isExportModifiedStuff()) ? $this->queryWhere . " AND (" : " AND (";

            // Apply Filtering Rules
            foreach ($this->filterRules as $rule) {
                if ( is_null($rule->parent_id) ) {
                    $this->parse_single_rule($rule);
                }
            }

            if ($this->meta_query || $this->tax_query) {
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->posts}.ID";
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
            $this->queryWhere = " AND ({$this->wpdb->posts}.ID NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }
        if ($this->isExportModifiedStuff() && ! empty(\XmlExportEngine::$exportRecord->registered_on)){
            $this->queryWhere .= " AND {$this->wpdb->posts}.post_modified > '" . \XmlExportEngine::$exportRecord->registered_on . "' ";
        }
    }

    /**
     * @param $rule
     * @return mixed|void
     */
    public function parse_single_rule($rule){
        
        apply_filters('wp_all_export_single_filter_rule', $rule);
        
        switch ($rule->element) {
            case 'ID':
            case 'post_parent':
            case 'post_author':
                $this->queryWhere .= "{$this->wpdb->posts}.$rule->element " . $this->parse_condition($rule, true);
                break;
            case 'post_status':
            case 'post_title':
            case 'post_content':
            case 'post_excerpt':
            case 'guid':
            case 'post_name':
            case 'menu_order':
                $this->queryWhere .= "{$this->wpdb->posts}.$rule->element " . $this->parse_condition($rule);
                break;
            case 'user_ID':
                $rule->element = 'post_author';
                $this->queryWhere .= "{$this->wpdb->posts}.$rule->element " . $this->parse_condition($rule, true);
                break;
            case 'user_login':
            case 'user_nicename':
            case 'user_email':
            case 'user_registered':
            case 'display_name':
            case 'first_name':
            case 'last_name':
            case 'nickname':
            case 'description':
            case 'wp_capabilities':

                $this->userWhere = " AND (";
                $this->userJoin  = array();
                $meta_query = false;

                switch ($rule->element) {
                    case 'wp_capabilities':
                        $meta_query = true;
                        $cap_key = $this->wpdb->prefix . 'capabilities';
                        $this->userJoin[] = " INNER JOIN {$this->wpdb->usermeta} ON ({$this->wpdb->usermeta}.user_id = {$this->wpdb->users}.ID) ";
                        $this->userWhere .= "{$this->wpdb->usermeta}.meta_key = '$cap_key' AND {$this->wpdb->usermeta}.meta_value " . $this->parse_condition($rule);
                        break;
                    case 'user_registered':
                        $this->parse_date_field( $rule );
                        $this->userWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule);
                        break;
                    case 'user_login':
                    case 'user_nicename':
                    case 'user_email':
                    case 'display_name':
                    case 'nickname':
                    case 'description':
                        $this->userWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule);
                        break;
                    default:

                        if (strpos($rule->element, "cf_") === 0)
                        {

                            $meta_query = true;
                            $meta_key = str_replace("cf_", "", $rule->element);

                            if ($rule->condition == 'is_empty') {
                                $this->userJoin[] = " LEFT JOIN {$this->wpdb->usermeta} ON ({$this->wpdb->usermeta}.user_id = {$this->wpdb->users}.ID AND {$this->wpdb->usermeta}.meta_key = '$meta_key') ";
                                $this->userWhere .= "{$this->wpdb->usermeta}.umeta_id " . $this->parse_condition($rule);
                            }
                            else {
                                $this->userJoin[] = " INNER JOIN {$this->wpdb->usermeta} ON ({$this->wpdb->usermeta}.user_id = {$this->wpdb->users}.ID) ";
                                $this->userWhere .= "{$this->wpdb->usermeta}.meta_key = '$meta_key' AND {$this->wpdb->usermeta}.meta_value " . $this->parse_condition($rule);
                            }
                        }
                        break;
                }

                $this->userWhere .= $meta_query ? " ) GROUP BY {$this->wpdb->users}.ID" : ")";

                add_action('pre_user_query', array(&$this, 'pre_user_query'), 10, 1);
                $userQuery = new \WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC') );
                remove_action('pre_user_query', array(&$this, 'pre_user_query'));

                $userIDs = array();

                foreach ( $userQuery->results as $user ) :
                    $userIDs[] = $user->ID;
                endforeach;

                if ( ! empty($userIDs)) {
                    $users_str = implode(",", $userIDs);
                    $this->queryWhere .= "{$this->wpdb->posts}.post_author IN ($users_str)";
                    if ( ! empty($rule->clause)) $this->queryWhere .= " " . $rule->clause . " ";
                }

                break;
            case 'post_date':
            case 'post_modified':
                $this->parse_date_field( $rule );
                $this->queryWhere .= "{$this->wpdb->posts}.$rule->element " . $this->parse_condition($rule);
                break;
            default:

                if (strpos($rule->element, "cf_") === 0) {
                    $this->meta_query = true;

                    $meta_key = $this->removePrefix($rule->element, "cf_");

                    if ($rule->condition == 'is_empty') {
                        $table_alias = (count($this->queryJoin) > 0) ? 'meta' . count($this->queryJoin) : 'meta';
                        $this->queryJoin[] = " LEFT JOIN {$this->wpdb->postmeta} AS $table_alias ON ($table_alias.post_id = {$this->wpdb->posts}.ID AND $table_alias.meta_key = '$meta_key') ";
                        $this->queryWhere .= "$table_alias.meta_id " . $this->parse_condition($rule);
                    } else {
                        if (in_array($meta_key, array('_completed_date'))) {
                            $this->parse_date_field($rule);
                        }
                        $table_alias = (count($this->queryJoin) > 0) ? 'meta' . count($this->queryJoin) : 'meta';
                        $this->queryJoin[] = " INNER JOIN {$this->wpdb->postmeta} AS $table_alias ON ({$this->wpdb->posts}.ID = $table_alias.post_id) ";
                        $this->queryWhere .= "$table_alias.meta_key = '$meta_key' AND $table_alias.meta_value " . $this->parse_condition($rule, false, $table_alias);
                    }

                }
                elseif (strpos($rule->element, "tx_") === 0){

                    if ( ! empty($rule->value) ){
                        $this->tax_query = true;
                        $tx_name = str_replace("tx_", "", $rule->element);

                        $terms = array();
                        $txs = explode(",", $rule->value);

                        foreach ($txs as $tx) {
                            if (is_numeric($tx)){
                                $terms[] = $tx;
                            }
                            else{
                                $term = term_exists($tx, $tx_name);
                                if (!is_wp_error($term)){
                                    $terms[] = $term['term_taxonomy_id'];
                                }
                            }
                        }

                        if ( ! empty($terms) ){

                            $terms_str = implode(",", $terms);

                            switch ($rule->condition) {
                                case 'in':
                                    $table_alias = (count($this->queryJoin) > 0) ? 'tr' . count($this->queryJoin) : 'tr';
                                    $this->queryJoin[] = " LEFT JOIN {$this->wpdb->term_relationships} AS $table_alias ON ({$this->wpdb->posts}.ID = $table_alias.object_id)";
                                    $this->queryWhere .= "$table_alias.term_taxonomy_id IN ($terms_str)";
                                    if ( ! empty($rule->clause)) $this->queryWhere .= " " . $rule->clause . " ";
                                    break;
                                case 'not_in':

                                    $this->queryWhere .= "{$this->wpdb->posts}.ID NOT IN (
                                      SELECT object_id
                                      FROM {$this->wpdb->term_relationships}
                                      WHERE term_taxonomy_id IN ($terms_str)
                                    )";
                                    if ( ! empty(\XmlExportEngine::$post_types) and class_exists('WooCommerce')) {
                                      if (@in_array("product", \XmlExportEngine::$post_types) || @in_array("shop_order", \XmlExportEngine::$post_types)) {
                                        $this->queryWhere .= " AND {$this->wpdb->posts}.post_parent NOT IN (
                                          SELECT object_id
                                          FROM {$this->wpdb->term_relationships}
                                          WHERE term_taxonomy_id IN ($terms_str)
                                        )";
                                      }
                                    }

                                    if ( ! empty($rule->clause)) $this->queryWhere .= " " . $rule->clause . " ";
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                        }
                    }
                }
                break;
        }
        $this->recursion_parse_query($rule);
    }

    /**
     * @param $obj
     */
    public function pre_user_query($obj ) {
        $obj->query_where .= $this->userWhere;

        if ( ! empty( $this->userJoin ) ) {
            $obj->query_from .= implode( ' ', array_unique( $this->userJoin ) );
        }
    }

    /**
     * @param $str
     * @param $prefix
     * @return string
     */
    private function removePrefix($str, $prefix)
    {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
            return $str;
        }
        return $str;
    }
}