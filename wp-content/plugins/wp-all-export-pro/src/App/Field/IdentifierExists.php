<?php

namespace Wpae\App\Field;


class IdentifierExists extends Field
{
    const SECTION = 'uniqueIdentifiers';
    
    public function getValue($snippetData)
    {
        $uniqueIdentifiersData = $this->feed->getSectionFeedData(self::SECTION);

        if($uniqueIdentifiersData['identifierExists'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($uniqueIdentifiersData['identifierExistsCV'], $snippetData);
        }

        return 'yes';
    }

    public function getFieldName()
    {
        return 'identifier_exists';
    }
}