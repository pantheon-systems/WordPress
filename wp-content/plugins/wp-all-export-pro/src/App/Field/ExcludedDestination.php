<?php

namespace Wpae\App\Field;

// This is not used right now
// Remove file if we decide not to use this field
class ExcludedDestination extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        if($advancedAttributes['excludedDestination'] == self::CUSTOM_VALUE_TEXT) {
            $excludedDestination = $advancedAttributes['excludedDestinationCV'];
        } else {
            $excludedDestination = $advancedAttributes['excludedDestination'];
        }

        return $excludedDestination;
    }

    public function getFieldName()
    {
        return 'excluded_destination';
    }
}