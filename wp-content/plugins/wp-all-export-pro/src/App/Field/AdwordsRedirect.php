<?php

namespace Wpae\App\Field;


class AdwordsRedirect extends Field
{
    const SECTION = 'advancedAttributes';
    
    public function getValue($snippetData)
    {
        $advancedAttributesData = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributesData['adwordsRedirect'])) {
            return '';
        }

        return $this->replaceSnippetsInValue($advancedAttributesData['adwordsRedirect'], $snippetData);
    }

    public function getFieldName()
    {
        return 'adwords_redirect';
    }
}