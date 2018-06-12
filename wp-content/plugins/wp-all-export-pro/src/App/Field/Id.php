<?php

namespace Wpae\App\Field;


class Id extends Field
{
    const SECTION = 'basicInformation';
    
    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);
        if(empty($basicInformationData['itemId'])) {
            return $this->entry->ID;
        } else {
            return $this->replaceSnippetsInValue($basicInformationData['itemId'], $snippetData);
        }

    }

    public function getFieldName()
    {
        return 'id';
    }
}