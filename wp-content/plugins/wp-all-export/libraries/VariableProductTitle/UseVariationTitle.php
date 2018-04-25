<?php

class VariableProductTitle_UseVariationTitle
{
    /** @var  string */
    protected $value;

    public function getTitle(WP_Post $current, WP_Post $parent)
    {
        return $current->post_title;
    }
}