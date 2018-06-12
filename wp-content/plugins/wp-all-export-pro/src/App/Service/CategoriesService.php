<?php

namespace Wpae\App\Service;


class CategoriesService
{
    public function getTaxonomyHierarchy($parent = 0)
    {
        $termsConfig = array(
            'taxonomy'     => 'product_cat',
            'hide_empty' => false,
            'parent' => $parent
        );

        $terms = \get_categories($termsConfig);

        $children = array();

        foreach ($terms as $term) {

            $item = array(
                'id' => $term->term_id,
                'title' => $term->name,
                'children' => $this->getTaxonomyHierarchy($term->term_id)
            );
            $children[] = $item;
        }

        return $children;
    }
}