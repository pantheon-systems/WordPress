<?php

namespace Wpae\App\Field;


class Multipack extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($advancedAttributes['multipack'])) {
            return '';
        }

        return $this->replaceSnippetsInValue($advancedAttributes['multipack'], $snippetData);
    }

    public function getFieldName()
    {
        return 'multipack';
    }
}