<?php

namespace Wpae\App\Field;


class Description extends Field
{
    const SECTION = 'basicInformation';

    private $basicInformationData = null;

    private $snippetData;

    public function getValue($snippetData)
    {
        $this->snippetData = $snippetData;
        $this->basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($this->entry->post_type === 'product') {
            return $this->getDescription($this->entry);
        }
        else if($this->entry->post_type === 'product_variation') {

            if($this->basicInformationData['useVariationDescriptionForVariableProducts']) {
                $variation_description = get_post_meta($this->entry->ID, '_variation_description');

                if(!empty($variation_description[0])) {
                    if(!empty($variation_description[0])) {
                        if(empty($variation_description[0])) {
                            $parentDescription = $this->getDescription(get_post($this->entry->post_parent));
                            return $parentDescription;
                        }
                        return $variation_description[0];
                    } else {
                        $parentDescription = $this->getDescription(get_post($this->entry->post_parent));
                        return $parentDescription;
                    }

                } else {
                    $parentDescription = $this->getDescription(get_post($this->entry->post_parent));

                    return $parentDescription;
                }
            } else {
                return $this->getDescription(get_post($this->entry->post_parent));
            }
        } else {
            throw new \Exception('Unknown export entity type');
        }
    }

    public function getFieldName()
    {
        return 'description';
    }

    /**
     * @param $product
     * @return mixed
     * @throws \Exception
     * @internal param $entry
     * @internal param $basicInformationData
     */
    private function getDescription($product)
    {
        if ($this->basicInformationData['itemDescription'] == 'productDescription') {
            return $product->post_content;
        } else if ($this->basicInformationData['itemDescription'] == 'productShortDescription') {
            return $product->post_excerpt;
        } else if ($this->basicInformationData['itemDescription'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($this->basicInformationData['itemDescriptionCV'], $this->snippetData);
        } else {
            throw new \Exception('Unknown field value');
        }
    }
}