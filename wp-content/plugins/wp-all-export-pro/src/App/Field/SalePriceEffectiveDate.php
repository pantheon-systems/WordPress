<?php

namespace Wpae\App\Field;


class SalePriceEffectiveDate extends Field
{
    const SECTION = 'availabilityPrice';

    const IN_STOCK = 'in stock';

    const OUT_OF_STOCK = 'out of stock';

    public function getValue($snippetData)
    {
        $availabilityPrice = $this->feed->getSectionFeedData(self::SECTION);

        if(isset($availabilityPrice['salePriceEffectiveDate'])) {
            return $this->replaceSnippetsInValue($availabilityPrice['salePriceEffectiveDate'], $snippetData);
        } else {
            return '';
        }

    }

    public function getFieldName()
    {
        return 'sale_price_effective_date';
    }

}