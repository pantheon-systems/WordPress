<?php

namespace Wpae\WordPress;


class Filters
{

    /**
     * @param $filter
     * @param array $args
     *
     * @return mixed
     */
    public function applyFilters($filter, $args)
    {
        $args = array_merge(
            array($filter),
            $args);

        $response = call_user_func_array('apply_filters', $args);

        return $response;
    }
}