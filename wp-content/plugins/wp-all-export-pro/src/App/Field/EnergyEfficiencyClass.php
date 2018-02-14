<?php

namespace Wpae\App\Field;


class EnergyEfficiencyClass extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['energyEfficiencyClass'])) {
            return '';
        }
        
        $energyEfficiencyClass = $advancedAttributes['energyEfficiencyClass'];
        $energyEfficiencyClass = $this->replaceSnippetsInValue($energyEfficiencyClass, $snippetData);
        
        return $this->replaceMappings($advancedAttributes['energyEfficiencyClassMappings'], $energyEfficiencyClass);
    }

    public function getFieldName()
    {
        return 'energy_efficiency_class';
    }
}