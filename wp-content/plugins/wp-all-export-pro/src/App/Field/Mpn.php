<?php

namespace Wpae\App\Field;


class Mpn extends Field
{
    const SECTION = 'uniqueIdentifiers';

    public function getValue($snippetData)
    {
        $uniqueIdentifiersData = $this->feed->getSectionFeedData(self::SECTION);

        return $this->replaceSnippetsInValue($uniqueIdentifiersData['mpn'], $snippetData);
    }

    public function getFieldName()
    {
        return 'mpn';
    }
    
}