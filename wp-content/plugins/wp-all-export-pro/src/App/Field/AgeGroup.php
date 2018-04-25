<?php

namespace Wpae\App\Field;


class AgeGroup extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['ageGroup'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['ageGroupAttribute'])) {
                $ageGroupAttribute = $detailedInformationData['ageGroupAttribute'];
                return $this->replaceSnippetsInValue($ageGroupAttribute, $snippetData);
            } else {
                return '';
            }

        } else if ($detailedInformationData['ageGroup'] == 'selectFromProductTaxonomies') {

            $categoryData = $this->feed->getSectionFeedData(self::SECTION);

            $categoryId = $this->getProductCategoryId($this->getProduct());

            if(isset($categoryData['ageGroupCats'][$categoryId])) {
                return $categoryData['ageGroupCats'][$categoryId];
            } else {
                return '';
            }

        } else if($detailedInformationData['ageGroup'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['ageGroupCV'], $snippetData);
        } else {
            throw new \Exception('Unknown vale '.$detailedInformationData['ageGroup'].' for field ageGroup');
        }
    }

    public function getFieldName()
    {
        return 'age_group';
    }

    private function getProductCategoryId($product)
    {
        $category = $this->getProductCategory($product);

        return $category->term_id;
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