<?php

namespace Wpae\App\Field;


class CustomLabel4 extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['customLabel4'])) {
            return '';
        }

        $customLabel = $this->replaceSnippetsInValue($advancedAttributes['customLabel4'], $snippetData);
        return $this->replaceMappings($advancedAttributes['customLabel4Mappings'], $customLabel);
    }

    public function getFieldName()
    {
        return 'custom_label_4';
    }
}