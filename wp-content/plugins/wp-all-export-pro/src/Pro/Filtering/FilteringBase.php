<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringBase
 * @package Wpae\Filtering
 */
abstract class FilteringBase implements FilteringInterface
{
    /**
     * @var \wpdb
     */
    public $wpdb;

    /**
     * @var array|bool|int|mixed|null
     */
    public $exportId;

    /**
     * @var string
     */
    protected $queryWhere = "";
    /**
     * @var array
     */
    protected $queryJoin = array();
    /**
     * @var string
     */
    protected $userWhere = "";
    /**
     * @var array
     */
    protected $userJoin = array();
    /**
     * @var
     */
    protected $options;
    /**
     * @var bool
     */
    protected $tax_query = false;
    /**
     * @var bool
     */
    protected $meta_query = false;
    /**
     * @var array
     */
    protected $filterRules = array();

    /**
     * FilteringBase constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->exportId = $this->getExportId();
        add_filter('wp_all_export_single_filter_rule', array(&$this, 'parse_rule_value'), 10, 1);
    }

    /**
     * @param array $args
     */
    public function init($args = array()){
        $this->options = $args;
        $this->filterRules = empty($this->options['filter_rules_hierarhy']) ? array() : json_decode($this->options['filter_rules_hierarhy']);
    }

    /**
     * @param $rule
     * @return mixed
     */
    abstract public function parse_single_rule($rule);

    /**
     *
     */
    abstract public function parse();

    /**
     *
     */
    abstract public function checkNewStuff();

    /**
     * @return bool
     */
    protected function isFilteringAllowed(){
        // do not apply filters for child exports
        if ( ! empty(\XmlExportEngine::$exportRecord->parent_id) ) {
            $this->queryWhere = \XmlExportEngine::$exportRecord->options['whereclause'];
            $this->queryJoin  = \XmlExportEngine::$exportRecord->options['joinclause'];
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @return bool
     */
    protected function isExportNewStuff(){
        return ( ! empty(\XmlExportEngine::$exportOptions['export_only_new_stuff']) and ! empty($this->exportId) && ! \PMXE_Plugin::isNewExport());
    }

    /**
     * @return bool
     */
    protected function isExportModifiedStuff(){
        return ( ! empty(\XmlExportEngine::$exportOptions['export_only_modified_stuff']) and ! empty($this->exportId) && ! \PMXE_Plugin::isNewExport());
    }

    /**
     * @param $rule
     */
    protected function parse_date_field(&$rule ){

        if (strpos($rule->value, "+") !== 0
            && strpos($rule->value, "-") !== 0
            && strpos($rule->value, "next") === false
            && strpos($rule->value, "last") === false
            && (strpos($rule->value, "second") !== false || strpos($rule->value, "minute") !== false || strpos($rule->value, "hour") !== false || (strpos($rule->value, "day") !== false && strpos($rule->value, "today") === false && strpos($rule->value, "yesterday") === false) || strpos($rule->value, "week") !== false || strpos($rule->value, "month") !== false || strpos($rule->value, "year") !== false))
        {
            $rule->value = "-" . trim(str_replace("ago", "", $rule->value));
        }

        $rule->value = strpos($rule->value, ":") !== false ? date("Y-m-d H:i:s", strtotime($rule->value)) : ( in_array($rule->condition, array('greater', 'equals_or_less')) ? date("Y-m-d", strtotime('+1 day', strtotime($rule->value))) : date("Y-m-d", strtotime($rule->value)));

    }

    /**
     * @param $parent_rule
     * @param $callback
     */
    protected function recursion_parse_query($parent_rule){
        $filter_rules_hierarchy = json_decode($this->options['filter_rules_hierarhy']);
        $sub_rules = array();
        foreach ($filter_rules_hierarchy as $j => $rule) if ($rule->parent_id == $parent_rule->item_id and $rule->item_id != $parent_rule->item_id) { $sub_rules[] = $rule; }
        if ( ! empty($sub_rules) ){
            $this->queryWhere .= "(";
            foreach ($sub_rules as $rule){
                $this->parse_single_rule($rule);
            }
            $this->queryWhere .= ")";
        }
    }

    /**
     * @param $rule
     * @param bool $is_int
     * @param bool $table_alias
     * @return string
     */
    protected function parse_condition($rule, $is_int = false, $table_alias = false){

        $value = $rule->value;
        $q = "";
        switch ($rule->condition) {
            case 'equals':
                if ( in_array($rule->element, array('post_date', 'comment_date', 'user_registered', 'user_role')) )
                {
                    $q = "LIKE '%". $value ."%'";
                }
                else
                {
                    $q = "= " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                }
                break;
            case 'not_equals':
                if ( in_array($rule->element, array('post_date', 'comment_date', 'user_registered', 'user_role')) )
                {
                    $q = "NOT LIKE '%". $value ."%'";
                }
                else
                {
                    $q = "!= " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                }
                break;
            case 'greater':
                $q = "> " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                break;
            case 'equals_or_greater':
                $q = ">= " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                break;
            case 'less':
                $q = "< " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                break;
            case 'equals_or_less':
                $q = "<= " . (($is_int or is_numeric($value)) ? $value : "'" . $value . "'");
                break;
            case 'contains':
                $q = "LIKE '%". $value ."%'";
                break;
            case 'not_contains':
                $q = "NOT LIKE '%". $value ."%'";
                break;
            case 'is_empty':
                $q = "IS NULL";
                break;
            case 'is_not_empty':
                $q = "IS NOT NULL";
                if ($table_alias) $q .= " AND $table_alias.meta_value <> '' ";
                break;
            default:
                # code...
                break;

        }

        if ( ! empty($rule->clause) ) $q .= " " . $rule->clause . " ";

        return $q;

    }

    /**
     * @param $rule
     * @return mixed
     */
    public function parse_rule_value($rule )
    {
        if ( preg_match("%^\[.*\]$%", $rule->value) )
        {
            $function = trim(trim($rule->value, "]"), "[");

            preg_match("/^(.+?)\((.*?)\)$/", $function, $match);

            if ( ! empty($match[1]) and function_exists($match[1]) )
            {
                // parse function arguments
                if ( ! empty($match[2]) )
                {
                    $arguments = array_map('trim', explode(',', $match[2]));

                    $rule->value = call_user_func_array($match[1], $arguments);
                }
                else
                {
                    $rule->value = call_user_func($match[1]);
                }
            }
        }

        return $rule;
    }

    /**
     * @return array|bool|int|mixed|null
     */
    public function getExportId(){
        $input  = new \PMXE_Input();
        $export_id = $input->get('id', 0);
        if (empty($export_id))
        {
            $export_id = $input->get('export_id', 0);
            if (empty($export_id)){
                $export_id = ( ! empty(\PMXE_Plugin::$session->update_previous)) ? \PMXE_Plugin::$session->update_previous : 0;
            }
            if (empty($export_id) and ! empty(\XmlExportEngine::$exportID)){
                $export_id = \XmlExportEngine::$exportID;
            }
        }
        return $export_id;
    }

    /**
     * __get function.
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        return $this->get( $key );
    }

    /**
     * Get a session variable
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return mixed value of session variable
     */
    public function get( $key, $default = null ) {
        return isset( $this->{$key} ) ? $this->{$key} : $default;
    }
}