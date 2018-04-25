<?php

namespace Wpae\App\Field;


class Pattern extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['pattern'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['patternAttribute'])){
                $patternAttribute = $detailedInformationData['patternAttribute'];
                return $this->replaceSnippetsInValue($patternAttribute, $snippetData);
            } else {
                return '';
            }

            
        } else if($detailedInformationData['pattern'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['patternCV'], $snippetData);
        } else {
            throw new \Exception('Unknown vale '.$detailedInformationData['pattern'].' for field pattern');
        }
    }

    public function getFieldName()
    {
        return 'pattern';
    }
}