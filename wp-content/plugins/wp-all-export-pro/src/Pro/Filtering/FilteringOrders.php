<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringOrders
 * @package Wpae\Pro\Filtering
 */
class FilteringOrders extends FilteringCPT
{

    /**
     * @var string
     */
    private $productsWhere = "";
    /**
     * @var array
     */
    private $productsjoin = array();

    /**
     *
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
                $this->queryWhere .= " ) GROUP BY {$this->wpdb->posts}.ID";
            }
            else {
                $this->queryWhere .= ")";
            }
        }
    }

    /**
     * @param $rule
     */
    public function parse_single_rule($rule) {

        // Filtering by Order meta data
        if (strpos($rule->element, "item_") === 0){
            apply_filters('wp_all_export_single_filter_rule', $rule);
            $rule->element = preg_replace('%^item_%', '', $rule->element);
            $table_prefix = $this->wpdb->prefix;

            switch ($rule->element){
                case '__product_sku':
                    $rule->element = 'cf__sku';
                    $this->filterByProducts($rule);
                    break;
                case '__product_title':
                    $rule->element = 'post_title';
                    $this->filterByProducts($rule);
                    break;
                case '__coupons_used':
                    $this->meta_query = true;
                    $item_alias = (count($this->queryJoin) > 0) ? 'order_item' . count($this->queryJoin) : 'order_item';
                    $this->queryJoin[] = " INNER JOIN {$table_prefix}woocommerce_order_items AS $item_alias ON ({$this->wpdb->posts}.ID = $item_alias.order_id) ";
                    $this->queryWhere .= "$item_alias.order_item_type = 'coupon' AND $item_alias.order_item_name " . $this->parse_condition($rule, false, $item_alias);
                    break;
                default:
                    $this->meta_query = true;
                    if ($rule->condition == 'is_empty'){
                        $item_alias = (count($this->queryJoin) > 0) ? 'order_item' . count($this->queryJoin) : 'order_item';
                        $item_meta_alias = (count($this->queryJoin) > 0) ? 'order_itemmeta' . count($this->queryJoin) : 'order_itemmeta';
                        $this->queryJoin[] = " LEFT JOIN {$table_prefix}woocommerce_order_items AS $item_alias ON ({$this->wpdb->posts}.ID = $item_alias.order_id) ";
                        $this->queryJoin[] = " LEFT JOIN {$table_prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id AND $item_meta_alias.meta_key = '{$rule->element}') ";
                        $this->queryWhere .= "$item_meta_alias.meta_id " . $this->parse_condition($rule);
                    }
                    else{
                        $item_alias = (count($this->queryJoin) > 0) ? 'order_item' . count($this->queryJoin) : 'order_item';
                        $item_meta_alias = (count($this->queryJoin) > 0) ? 'order_itemmeta' . count($this->queryJoin) : 'order_itemmeta';
                        $this->queryJoin[] = " INNER JOIN {$table_prefix}woocommerce_order_items AS $item_alias ON ({$this->wpdb->posts}.ID = $item_alias.order_id) ";
                        $this->queryJoin[] = " INNER JOIN {$table_prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id) ";
                        $this->queryWhere .= "$item_meta_alias.meta_key = '{$rule->element}' AND $item_meta_alias.meta_value " . $this->parse_condition($rule, false, $item_meta_alias);
                    }
                    break;
            }
            $this->recursion_parse_query($rule);
            return;
        }

        // Filtering by Order Items data
        if (strpos($rule->element, "product_") === 0){
            // Filter Orders by order item data
            apply_filters('wp_all_export_single_filter_rule', $rule);
            $this->filterByProducts($rule);
            $this->recursion_parse_query($rule);
            return;
        }

        parent::parse_single_rule($rule);
    }

    /**
     * @param $rule
     */
    private function filterByProducts($rule){

        $rule->element = preg_replace('%^product_%', '', $rule->element);

        $mapping = array(
            'content' => 'post_content',
            'excerpt' => 'post_excerpt',
            'date'    => 'post_date'
        );

        if (!empty($mapping[$rule->element])) $rule->element = $mapping[$rule->element];

        $filter_args = array(
            'filter_rules_hierarhy' => json_encode(array($rule)),
            'product_matching_mode' => 'strict',
            'taxonomy_to_export' => ''
        );

        $productsFilter = new FilteringCPT();
        $productsFilter->init($filter_args);
        $productsFilter->parse();

        $this->productsWhere = $productsFilter->get('queryWhere');
        $this->productsjoin  = $productsFilter->get('queryJoin');

        remove_all_actions('parse_query');
        remove_all_actions('pre_get_posts');
        remove_all_filters('posts_clauses');

        add_filter('posts_join', array(&$this, 'posts_join'), 10, 1);
        add_filter('posts_where', array(&$this, 'posts_where'), 10, 1);
        $productsQuery = new \WP_Query( array( 'post_type' => array('product', 'product_variation'), 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC', 'posts_per_page' => -1 ));

        $ids = array();
        while ( $productsQuery->have_posts() ) {
            $productsQuery->the_post();
            $ids[] = get_the_ID();
        }

        remove_filter('posts_where', array(&$this, 'posts_where'));
        remove_filter('posts_join', array(&$this, 'posts_join'));

        if (!empty($ids)){
            $this->meta_query = true;
            $table_prefix = $this->wpdb->prefix;
            $ids_str = implode(",", $ids);
            $item_alias = (count($this->queryJoin) > 0) ? 'order_item' . count($this->queryJoin) : 'order_item';
            $item_meta_alias = (count($this->queryJoin) > 0) ? 'order_itemmeta' . count($this->queryJoin) : 'order_itemmeta';
            $this->queryJoin[] = " INNER JOIN {$table_prefix}woocommerce_order_items AS $item_alias ON ({$this->wpdb->posts}.ID = $item_alias.order_id) ";
            $this->queryJoin[] = " INNER JOIN {$table_prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id) ";
            $this->queryWhere .= "($item_meta_alias.meta_key = '_product_id' OR $item_meta_alias.meta_key = '_variation_id') AND $item_meta_alias.meta_value IN ($ids_str)";
        }
    }

    /**
     * @param $where
     * @return string
     */
    public function posts_where($where)
    {
        if ( ! empty($this->productsWhere) ) $where .= $this->productsWhere;
        return $where;
    }

    /**
     * @param $join
     * @return string
     */
    public function posts_join($join){
        if ( ! empty($this->productsjoin) ) {
            $join .= implode( ' ', array_unique( $this->productsjoin ) );
        }
        return $join;
    }
}