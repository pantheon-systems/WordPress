<?php

namespace Wpae\App\Field;


class Color extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['color'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['colorAttribute'])) {
                $colorAttribute = $detailedInformationData['colorAttribute'];
                $color = $this->replaceSnippetsInValue($colorAttribute, $snippetData);

                // Use max 3 colors
                $colors = explode(',', $color);
                $colors = $sliced_array = array_slice($colors, 0, 3);

                return implode('/',$colors);
            } else {
                return '';
            }

        } else if($detailedInformationData['color'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['colorCV'], $snippetData);
        } else {
            throw new \Exception('Unknown value '.$detailedInformationData['color'].' for field color');
        }
    }

    public function getFieldName()
    {
        return 'color';
    }
}