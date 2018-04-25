<?php

class VariableProductTitle_TitleProcessor
{
    /** @var  string */
    protected $value;

    public function process(WP_Post $current, WP_Post $parent)
    {
        return $current->post_title;
    }
}