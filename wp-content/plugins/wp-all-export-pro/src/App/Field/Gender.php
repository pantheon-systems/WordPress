<?php

namespace Wpae\App\Field;


class Gender extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if ($detailedInformationData['gender'] == 'selectProductTaxonomies') {

            $categoryData = $this->feed->getSectionFeedData(self::SECTION);

            $categoryId = $this->getProductCategoryId($this->getProduct());
            
            if(isset($categoryData['genderCats'][$categoryId])) {
                return $categoryData['genderCats'][$categoryId];
            } else {
                return '';
            }
            
        } else if ($detailedInformationData['gender'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['genderAttribute'])) {
                $genderAttribute = $detailedInformationData['genderAttribute'];
            } else {
                $genderAttribute = '';
            }
            return $this->replaceSnippetsInValue($genderAttribute, $snippetData);

        }
        else if ($detailedInformationData['gender'] == self::CUSTOM_VALUE_TEXT) {

            return $this->replaceSnippetsInValue($detailedInformationData['genderCV'], $snippetData);

        } else if ($detailedInformationData['gender'] == 'autodetectBasedOnProductTaxonomies') {

            $menValues = array('men', 'man', 'male', 'gentleman', 'gents');
            $womenValues = array('women', 'woman', 'female', 'ladies');
            $unisexValues = array('unisex');

            $product = $this->getProduct();

            $autodetect = '';

            $productCategories = get_the_terms($product->ID, 'product_cat');
            if (!is_array($productCategories) || !count($productCategories)) {
                return $autodetect;
            }

            foreach($productCategories as $attribute) {

                $attributeNameValue = $attribute->name;

                foreach($womenValues as $womenValue) {
                    if(strpos($attributeNameValue, $womenValue) !== false) {
                        // We do this so we don't match 'male next'
                        $attributeNameValue = str_replace($womenValue,'', $attributeNameValue);
                        $autodetect = 'female';
                    }
                }

                foreach($menValues as $menValue) {
                    if(strpos($attributeNameValue, $menValue) !== false) {
                        $autodetect = 'male';
                    }
                }

                foreach($unisexValues as $unisexValue) {
                    if(strpos($attributeNameValue, $unisexValue) !== false) {
                        $autodetect = 'unisex';
                    }
                }
            }

            if($autodetect === '') {
                if($detailedInformationData['genderAutodetect'] == 'setToUnisex') {
                    $autodetect = 'unisex';
                }
            }
            return $autodetect;
        }
        else {
            throw new \Exception('Unknown vale '.$detailedInformationData['gender'].' for field gender');
        }
    }

    public function getFieldName()
    {
        return 'gender';
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