<?php

namespace Wpae\App\Field;


class Link extends Field
{
    const SECTION = 'basicInformation';

    public function getValue($snippetData)
    {
        $link = '';

        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($basicInformationData['itemLink'] == self::CUSTOM_VALUE_TEXT) {
            $link = $this->replaceSnippetsInValue($basicInformationData['itemLinkCV'], $snippetData);
        }
        else if($basicInformationData['itemLink'] == 'productLink') {

            if ($this->entry->post_type === 'product_variation' && $basicInformationData['addVariationAttributesToProductUrl']) {
                $product = wc_get_product($this->entry->ID);
                $link = $product->get_permalink();
            } else if (
                ($this->entry->post_type === 'product_variation' && !$basicInformationData['addVariationAttributesToProductUrl'])
                || $this->entry->post_type === 'product'
            ) {

                $link = get_permalink($this->entry);
            }
        } else {
            throw new \Exception('Unknown field value ' . $basicInformationData['itemLink'] . ' for premalink');
        }

        return $link;
    }

    public function getFieldName()
    {
        return 'link';
    }
}