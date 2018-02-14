<?php

namespace Wpae\App\Field;


class AvailabilityDate extends Field
{
    const SECTION = 'availabilityPrice';

    const IN_STOCK = 'in stock';

    const OUT_OF_STOCK = 'out of stock';

    public function getValue($snippetData)
    {
        $availabilityPrice = $this->feed->getSectionFeedData(self::SECTION);

        if(isset($availabilityPrice['availabilityDate'])) {
            return $this->replaceSnippetsInValue($availabilityPrice['availabilityDate'], $snippetData);
        } else {
            return '';
        }
    }

    public function getFieldName()
    {
        return 'availability_date';
    }

}