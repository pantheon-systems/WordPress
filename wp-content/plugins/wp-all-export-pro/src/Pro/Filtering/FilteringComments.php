<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringComments
 * @package Wpae\Pro\Filtering
 */
class FilteringComments extends FilteringBase
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
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->comments}.comment_ID";
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
            $this->queryWhere = " AND ({$this->wpdb->comments}.comment_ID NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }
    }

    /**
     * @param $rule
     */
    public function parse_single_rule($rule) {
        apply_filters('wp_all_export_single_filter_rule', $rule);
        switch ($rule->element) {
            case 'comment_ID':
            case 'comment_post_ID':
            case 'comment_karma':
            case 'user_id':
            case 'comment_parent':
                $this->queryWhere .= "{$this->wpdb->comments}.$rule->element " . $this->parse_condition($rule, true);
                break;
            case 'comment_date':
                $this->parse_date_field( $rule );
                $this->queryWhere .= "{$this->wpdb->comments}.$rule->element " . $this->parse_condition($rule);
                break;
            case 'comment_author':
            case 'comment_author_email':
            case 'comment_author_url':
            case 'comment_author_IP':
            case 'comment_approved':
            case 'comment_agent':
            case 'comment_type':
            case 'comment_content':
                $this->queryWhere .= "{$this->wpdb->users}.$rule->element " . $this->parse_condition($rule);
                break;
            default:
                if (strpos($rule->element, "cf_") === 0)
                {
                    $this->meta_query = true;
                    $meta_key = str_replace("cf_", "", $rule->element);

                    if ($rule->condition == 'is_empty') {
                        $this->queryJoin[] = " LEFT JOIN {$this->wpdb->commentmeta} ON ({$this->wpdb->commentmeta}.comment_id = {$this->wpdb->comments}.comment_ID AND {$this->wpdb->commentmeta}.meta_key = '$meta_key') ";
                        $this->queryWhere .= "{$this->wpdb->commentmeta}.meta_id " . $this->parse_condition($rule);
                    }
                    else {
                        $this->queryJoin[] = " INNER JOIN {$this->wpdb->commentmeta} ON ({$this->wpdb->commentmeta}.comment_id = {$this->wpdb->comments}.comment_ID) ";
                        $this->queryWhere .= "{$this->wpdb->commentmeta}.meta_key = '$meta_key' AND {$this->wpdb->commentmeta}.meta_value " . $this->parse_condition($rule);
                    }

                }
                break;
        }
        $this->recursion_parse_query($rule);
    }
}