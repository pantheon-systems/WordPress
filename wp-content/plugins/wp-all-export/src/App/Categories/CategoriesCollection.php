<?php

namespace Wpae\App\Categories;


class CategoriesCollection
{
    private $categories;

    public function __construct($categories)
    {
        $this->categories = array($categories);
    }

    public function findCategory($categoryToFind)
    {
        return $this->findCategoryRecursive($categoryToFind, $this->categories);
    }

    private function findCategoryRecursive($categoryToFind, $categories)
    {
        foreach($categories as $category) {
            if($category['title'] == $categoryToFind) {
                return $category;
            }

            $result =  $this->findCategoryRecursive($categoryToFind, $category['children']);

            if($result) {
                return $result;
            }
        }

        return false;
    }
}