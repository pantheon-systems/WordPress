<?php

namespace Wpae\App\Field;


class Gtin extends Field
{
    const SECTION = 'uniqueIdentifiers';
    
    public function getValue($snippetData)
    {
        $uniqueIdentifiersData = $this->feed->getSectionFeedData(self::SECTION);

        return $this->replaceSnippetsInValue($uniqueIdentifiersData['gtin'], $snippetData);
    }

    public function getFieldName()
    {
        return 'gtin';
    }


}