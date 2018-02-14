<?php

namespace Wpae\App\Field;


class Brand extends Field
{
    const SECTION = 'uniqueIdentifiers';

    public function getValue($snippetData)
    {
        $uniqueIdentifiersData = $this->feed->getSectionFeedData(self::SECTION);

        return $this->replaceSnippetsInValue($uniqueIdentifiersData['brand'], $snippetData);
    }

    public function getFieldName()
    {
        return 'brand';
    }
}