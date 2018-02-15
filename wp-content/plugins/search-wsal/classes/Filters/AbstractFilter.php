<?php

abstract class WSAL_AS_Filters_AbstractFilter {
    /**
     * Returns true if this filter has suggestions for this query.
     * @param string $query The part of query to check.
     * @return boolean If filter has suggestions for query or not.
     */
    public abstract function IsApplicable($query);
    
    /**
     * @return array List of filter prefixes (the stuff before the colon).
     */
    public abstract function GetPrefixes();
    
    /**
     * @return WSAL_AS_Filters_AbstractWidget[] List of widgets to be used in UI.
     */
    public abstract function GetWidgets();
    
    /**
     * @return string Filter name (used in UI).
     */
    public abstract function GetName();
    
    /**
     * Allow this filter to change the DB query according to the search value (usually a value from GetOptions()).
     * @param WSAL_DB_Query $query Database query for selecting occurrenes.
     * @param string $prefix The filter name (filter string prefix).
     * @param string $value The filter value (filter string suffix).
     */
    public abstract function ModifyQuery($query, $prefix, $value);
    
    /**
     * Whether to print title for filter or not.
     * @var boolean
     */
    public $IsTitled = false;
    
    /**
     * Renders filter widgets.
     */
    public function Render(){
        if($this->IsTitled){
            ?><strong><?php echo $this->GetName(); ?></strong><?php
        }
        foreach ($this->GetWidgets() as $widget) {
            ?><div class="wsal-as-filter-widget"><?php
                $widget->Render();
            ?></div><?php
        }
    }
    
    /**
     * @return string Generates a widget name.
     */
    public function GetSafeName(){
        return strtolower(get_class($this));
    }
}
