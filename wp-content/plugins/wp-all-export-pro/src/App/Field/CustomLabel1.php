<?php

namespace Wpae\App\Field;


class CustomLabel1 extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['customLabel1'])) {
            return '';
        }

        $customLabel = $this->replaceSnippetsInValue($advancedAttributes['customLabel1'], $snippetData);

        return $this->replaceMappings($advancedAttributes['customLabel1Mappings'], $customLabel);
    }

    public function getFieldName()
    {
        return 'custom_label_1';
    }
}