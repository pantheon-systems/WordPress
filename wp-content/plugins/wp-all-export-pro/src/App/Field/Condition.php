<?php

namespace Wpae\App\Field;


class Condition extends Field
{
    const SECTION = 'basicInformation';
    
    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        $condition = $basicInformationData['condition'];
        $mappings = $basicInformationData['conditionMappings'];

        $condition = $this->replaceSnippetsInValue($condition, $snippetData);
        $condition = $this->replaceMappings($mappings, $condition);

        return $condition;
    }

    public function getFieldName()
    {
        return 'condition';
    }
}