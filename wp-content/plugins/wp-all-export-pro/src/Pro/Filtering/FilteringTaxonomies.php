<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringTaxonomies
 * @package Wpae\Pro\Filtering
 */
class FilteringTaxonomies extends FilteringBase
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
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->terms}.term_id";
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
            $this->queryWhere = " AND ({$this->wpdb->terms}.term_id NOT IN (SELECT post_id FROM " . $postList->getTable() . " WHERE export_id = '". $this->exportId ."'))";
        }
    }

    /**
     * @param $rule
     */
    public function parse_single_rule($rule) {
        apply_filters('wp_all_export_single_filter_rule', $rule);
        switch ($rule->element) {
            case 'term_id':
            case 'term_group':
                $this->queryWhere .= "t." . $rule->element . " " . $this->parse_condition($rule, true);
                break;
            case 'term_name':
            case 'term_slug':
                $this->queryWhere .= "t." . str_replace("term_", "", $rule->element) . " " . $this->parse_condition($rule);
                break;
            case 'term_parent_id':
                switch ($rule->condition){
                    case 'is_empty':
                        $rule->value = 0;
                        $rule->condition = 'equals';
                        break;
                    case 'is_not_empty':
                        $rule->value = 0;
                        $rule->condition = 'not_equals';
                        break;
                }
                $this->queryWhere .= "tt.parent " . $this->parse_condition($rule);
                break;
            case 'term_parent_name':

                switch ($rule->condition){
                    case 'contains':
                        $result = new \WP_Term_Query( array( 'taxonomy' => $this->options['taxonomy_to_export'], 'name__like' => $rule->value, 'hide_empty' => false));
                        $parent_terms = $result->get_terms();
                        if ($parent_terms){
                            $parent_term_ids = array();
                            foreach ($parent_terms as $p_term){
                                $parent_term_ids[] = $p_term->term_id;
                            }
                            $parent_term_ids_str = implode(",", $parent_term_ids);
                            $this->queryWhere .= "tt.parent IN ($parent_term_ids_str)";
                        }
                        break;
                    case 'not_contains':
                        $result = new \WP_Term_Query( array( 'taxonomy' => $this->options['taxonomy_to_export'], 'name__like' => $rule->value, 'hide_empty' => false));
                        $parent_terms = $result->get_terms();
                        if ($parent_terms){
                            $parent_term_ids = array();
                            foreach ($parent_terms as $p_term){
                                $parent_term_ids[] = $p_term->term_id;
                            }
                            $parent_term_ids_str = implode(",", $parent_term_ids);
                            $this->queryWhere .= "tt.parent NOT IN ($parent_term_ids_str)";
                        }
                        break;
                    default:

                        switch ($rule->condition){
                            case 'is_empty':
                                $rule->value = 0;
                                $rule->condition = 'equals';
                                break;
                            case 'is_not_empty':
                                $rule->value = 0;
                                $rule->condition = 'not_equals';
                                break;
                            default:
                                $parent_term = get_term_by('name', $rule->value, $this->options['taxonomy_to_export']);
                                if ($parent_term){
                                    $rule->value = $parent_term->term_id;
                                }
                                break;
                        }

                        $this->queryWhere .= "tt.parent " . $this->parse_condition($rule);
                        break;
                }
                break;
            case 'term_parent_slug':

                switch ($rule->condition){
                    case 'is_empty':
                        $rule->value = 0;
                        $rule->condition = 'equals';
                        break;
                    case 'is_not_empty':
                        $rule->value = 0;
                        $rule->condition = 'not_equals';
                        break;
                    default:
                        $parent_term = get_term_by('slug', $rule->value, $this->options['taxonomy_to_export']);
                        if ($parent_term){
                            $rule->value = $parent_term->term_id;
                        }
                        break;
                }
                $this->queryWhere .= "tt.parent " . $this->parse_condition($rule);
                break;
            case 'term_posts_count':
                $this->queryWhere .= "tt.count " . $this->parse_condition($rule);
                break;
            default:
                if (strpos($rule->element, "cf_") === 0)
                {
                    $this->meta_query = true;
                    $meta_key = str_replace("cf_", "", $rule->element);

                    if ($rule->condition == 'is_empty'){
                        $this->queryJoin[] = " LEFT JOIN {$this->wpdb->termmeta} ON ({$this->wpdb->termmeta}.term_id = t.term_id AND {$this->wpdb->termmeta}.meta_key = '$meta_key') ";
                        $this->queryWhere .= "{$this->wpdb->termmeta}.meta_id " . $this->parse_condition($rule);
                    }
                    else {
                        $this->queryJoin[] = " INNER JOIN {$this->wpdb->termmeta} ON ({$this->wpdb->termmeta}.term_id = t.term_id) ";
                        $this->queryWhere .= "{$this->wpdb->termmeta}.meta_key = '$meta_key' AND {$this->wpdb->termmeta}.meta_value " . $this->parse_condition($rule);
                    }

                }
                break;
        }
        $this->recursion_parse_query($rule);
    }
}