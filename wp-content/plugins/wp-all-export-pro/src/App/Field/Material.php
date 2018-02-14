<?php

namespace Wpae\App\Field;


class Material extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['material'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['materialAttribute'])) {
                $materialAttribute = $detailedInformationData['materialAttribute'];
                return $this->replaceSnippetsInValue($materialAttribute, $snippetData);
            } else {
                return '';
            }


        } else if($detailedInformationData['material'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['materialCV'], $snippetData);
        } else {
            throw new \Exception('Unknown vale '.$detailedInformationData['material'].' for field material');
        }
    }

    public function getFieldName()
    {
        return 'material';
    }
}