<?php

namespace Wpae\App\Field;


class CustomLabel2 extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['customLabel2'])) {
            return '';
        }

        $customLabel = $this->replaceSnippetsInValue($advancedAttributes['customLabel2'], $snippetData);
        return $this->replaceMappings($advancedAttributes['customLabel2Mappings'], $customLabel);
    }

    public function getFieldName()
    {
        return 'custom_label_2';
    }
}