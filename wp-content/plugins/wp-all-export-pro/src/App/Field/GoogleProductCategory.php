<?php

namespace Wpae\App\Field;

class GoogleProductCategory extends Field
{
    const SECTION = 'productCategories';

    public function getValue($snippetData)
    {
        $categoryData = $this->feed->getSectionFeedData(self::SECTION);

        if($categoryData['productCategories'] == 'mapProductCategories') {

            $categoryId = $this->getProductCategoryId($this->getProduct());

            if(isset($categoryData['catMappings'][$categoryId]['id'])) {
                return $categoryData['catMappings'][$categoryId]['id'];
            } else {
                return '';
            }

        } else if($categoryData['productCategories'] == 'useWooCommerceProductCategories') {
            return $this->getProductCategoryName($this->getProduct());
        } else if($categoryData['productCategories'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($categoryData['productCategoriesCV'], $snippetData);
        } else {
            throw new \Exception('Unknown value '.$categoryData['productCategories'].' for field product categories');
        }
    }

    public function getFieldName()
    {
        return 'google_product_category';
    }

    /**
     * @return string
     */
    private function getProductCategoryId($product)
    {
        $category = $this->getProductCategory($product);

        if(is_object($category)) {
            return $category->term_id;
        } else {
            return '';
        }
    }

    private function getProductCategoryName($product)
    {
        $category = $this->getProductCategory($product);

        return $category->name;
    }

    private function getProductCategory($product)
    {
        $productCategories = get_the_terms($product->ID, 'product_cat');
        if (!is_array($productCategories) || !count($productCategories)) {
            return '';
        }

        // loop through each cat
        foreach ($productCategories as $category) {
            // get the children (if any) of the current cat
            $children = get_categories(array('taxonomy' => 'product_cat', 'parent' => $category->term_id));

            if (count($children) == 0) {
                // if no children, then echo the category name.
                return $category;
            }
        }

        $lastCategory = $productCategories[0];

        return $lastCategory;

    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getProduct()
    {
        if ($this->entry->post_type == 'product') {
            return $this->entry;
        } else if ($this->entry->post_type == 'product_variation') {
            return get_post($this->entry->post_parent);
        }

        throw new \Exception('Unknown product type');
    }

}