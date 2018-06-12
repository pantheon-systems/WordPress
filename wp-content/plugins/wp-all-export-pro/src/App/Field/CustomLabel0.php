<?php

namespace Wpae\App\Field;


class CustomLabel0 extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);
        
        if(!isset($advancedAttributes['customLabel0'])) {
            return '';
        }
        
        $customLabel = $this->replaceSnippetsInValue($advancedAttributes['customLabel0'], $snippetData);
        return $this->replaceMappings($advancedAttributes['customLabel0Mappings'], $customLabel);
    }

    public function getFieldName()
    {
        return 'custom_label_0';
    }
}