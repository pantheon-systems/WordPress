<?php

class VariableProductTitle_UseParentTitle
{
    /** @var  string */
    protected $value;

    public function getTitle(WP_Post $current, WP_Post $parent)
    {
        return  false;
    }
}