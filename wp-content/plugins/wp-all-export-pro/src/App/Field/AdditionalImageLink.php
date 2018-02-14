<?php

namespace Wpae\App\Field;


class AdditionalImageLink extends Field
{
    const SECTION = 'basicInformation';

    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);
        $product = wc_get_product($this->entry->ID);

        if($basicInformationData['additionalImageLink'] == 'productImages') {
            
            $attachment_ids = $product->get_gallery_attachment_ids();

            if(is_array($attachment_ids) && count($attachment_ids)) {
                return wp_get_attachment_url($attachment_ids[0]);
            }

        } else if($basicInformationData['additionalImageLink'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($basicInformationData['additionalImageLinkCV'], $snippetData);
        } else {
            throw new \Exception('Unknown value '.$basicInformationData['additionalImageLink']. ' for additional image link');
        }

    }

    public function getFieldName()
    {
        return 'additional_image_link';
    }

}