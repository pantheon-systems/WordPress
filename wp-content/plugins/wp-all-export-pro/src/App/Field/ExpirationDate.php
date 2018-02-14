<?php

namespace Wpae\App\Field;


class ExpirationDate extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['expirationDate'])) {
            return '';
        }
        
        return $this->replaceSnippetsInValue($advancedAttributes['expirationDate'], $snippetData);
    }

    public function getFieldName()
    {
        return 'expiration_date';
    }
}