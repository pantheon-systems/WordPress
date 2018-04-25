<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringUsers
 * @package Wpae\Pro\Filtering
 */
class FilteringFree extends FilteringBase
{
    /**
     * @return bool
     */
    public function parse(){
        return false;
    }

    /**
     *
     */
    public function checkNewStuff(){
        return false;
    }

    /**
     * @param $rule
     */
    public function parse_single_rule($rule){
        return false;
    }
}