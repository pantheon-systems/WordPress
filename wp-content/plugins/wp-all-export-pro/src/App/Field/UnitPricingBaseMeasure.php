<?php

namespace Wpae\App\Field;


class UnitPricingBaseMeasure extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        $unitPricingBaseMeasure = $this->replaceSnippetsInValue($advancedAttributes['unitPricingBaseMeasure'],$snippetData);

        if($unitPricingBaseMeasure) {
            return $unitPricingBaseMeasure." ".$advancedAttributes['unitPricingBaseMeasureUnit'];
        } else {
            return "";
        }
    }

    public function getFieldName()
    {
        return 'unit_pricing_base_measure';
    }
}