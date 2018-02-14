<?php

namespace Wpae\App\Field;


class ImageLink extends Field
{
    const SECTION = 'basicInformation';

    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if ($basicInformationData['itemImageLink'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($basicInformationData['itemImageLinkCV'], $snippetData);
        } else {
            if ($this->entry->post_type == 'product_variation') {
                if ($basicInformationData['useVariationImage']) {
                    $variationImage = $this->getImage($this->entry);
                    if (empty($variationImage)) {
                        $variationImage = $this->getImage($this->entry->post_parent);
                    }
                    return $variationImage;

                } else {
                    return $this->getImage($this->entry->post_parent);
                }
            }

            if (has_post_thumbnail($this->entry->ID)) {
                $image = $this->getImage($this->entry);
                return $image;
            }
        }

        return '';
    }

    public function getFieldName()
    {
        return 'image_link';
    }

    /**
     * @param $entry
     * @return mixed
     */
    private function getImage($entry)
    {
        if (is_object($entry)) {
            if ($entry->post_type == 'product_variation') {
                $variation = new \WC_Product_Variation($entry->ID);
                if (is_object($variation)) {
                    $imageId = $variation->get_image_id();
                } else {
                    $imageId = '';
                }
            } else {
                $product = new \WC_Product($entry->ID);
                $imageId = $product->get_image_id();
            }

            if ($imageId) {
                $imageUrl = wp_get_attachment_url($imageId);
                return $imageUrl;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
}