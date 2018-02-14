<?php

namespace Wpae\App\Controller;

use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;

class GoogleCategoriesController extends BaseController
{
    public function getAction(Request $request)
    {
        global $wpdb;

        $tablePrefix = $this->getTablePrefix();

        $response = array();

        $search = $request->get('search', '');
        $parent = $request->get('parent');

        $searchString = '';

        if(!is_null($parent)) {
            $searchString .= $wpdb->prepare(" AND `parent_id` = %d ", $parent);
        }

        if($search) {
            $searchString = $wpdb->prepare(" AND `name` LIKE %s LIMIT 50", '%'.$wpdb->esc_like($search).'%');
        }

        $querystr = "SELECT * FROM `{$tablePrefix}google_cats` WHERE 1=1 $searchString";
        $pageposts = $wpdb->get_results($querystr, ARRAY_A);

        // If it's a search find the parents of the categories
        if($search) {
            $parents = [];

            foreach($pageposts as $category) {

                if(!$category['parent_id']) {
                    $parents = array_merge($parents, [$category]);
                }

                $sql = "SELECT * FROM `{$tablePrefix}google_cats` WHERE `id` = $category[parent_id]";
                $results = $wpdb->get_results($sql, ARRAY_A);

                foreach ($results as &$result) {
                    $result['children'] = [$this->processCategory($category, $search)];
                }

                $parents = array_merge($parents, $results);
            }

            $pageposts = $parents;
        }

        foreach($pageposts as $category) {
            $catItem = $this->processCategory($category, $search);
            $response[] = $catItem;
        }

        if(!$parent) {
            $response = array('name' => 'Root', 'children' => $response);
        }

        return new JsonResponse($response);
    }

    /**
     * @param $categoryId
     * @return mixed
     * @internal param $category
     * @internal param $wpdb
     */
    private function categoryHasChildren($categoryId)
    {
        global $wpdb;

        $tablePrefix = $this->getTablePrefix();

        $categoryId = intval($categoryId);

        $childrenQuerystr = "SELECT COUNT(*) as hasChildren FROM `{$tablePrefix}google_cats` WHERE `parent_id` = %d";
        $childrenQuerystr = $wpdb->prepare($childrenQuerystr, $categoryId);
        $hasChildren = $wpdb->get_results($childrenQuerystr, ARRAY_A);
        $hasChildren = $hasChildren[0]['hasChildren'];
        return $hasChildren;
    }

    /**
     * @param $category
     * @param $search
     * @return array
     */
    private function processCategory($category, $search)
    {
        //TODO: Optimize this and prepare statements
        $hasChildren = $this->categoryHasChildren($category['id']);
        if ($search) {
            $categoryName = preg_replace("/".preg_quote($search)."/i", "<b>\$0</b>", $category['name']);
        } else {
            $categoryName = $category['name'];
        }

        $catItem = array(
            'name' => $categoryName,
            'hasChildren' => $hasChildren,
            'id' => $category['id'],
            'opened' => false,
            'visible' => true
        );

        if(isset($category['parentName'])){
            $catItem['parentName'] = $category['parentName'];
        } else {
            $catItem['parentName'] = '';
        }

        if (isset($category['children'])) {
            $catItem['children'] = $category['children'];
            $catItem['opened'] = true;
            return $catItem;
        }
        return $catItem;
    }

    /**
     * @return string
     */
    private function getTablePrefix()
    {
        $plugin = \PMXE_Plugin::getInstance();
        $tablePrefix = $plugin->getTablePrefix();
        return $tablePrefix;
    }
}