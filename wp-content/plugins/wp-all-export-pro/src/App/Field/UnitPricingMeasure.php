<?php

namespace Wpae\App\Field;


class UnitPricingMeasure extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['unitPricingMeasure'])) {
            return '';
        }
        
        $unitPricingMeasure = $this->replaceSnippetsInValue($advancedAttributes['unitPricingMeasure'], $snippetData);

        if($unitPricingMeasure){
            return $unitPricingMeasure." ".$advancedAttributes['unitPricingBaseMeasureUnit'];
        } else {
            return "";
        }

    }

    public function getFieldName()
    {
        return 'unit_pricing_measure';
    }
}