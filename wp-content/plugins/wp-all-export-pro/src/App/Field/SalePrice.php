<?php

namespace Wpae\App\Field;


class SalePrice extends Field
{
    const SECTION = 'availabilityPrice';

    public function getValue($snippetData)
    {
        $availabilityPriceData = $this->feed->getSectionFeedData(self::SECTION);

        if($availabilityPriceData['salePrice'] == self::CUSTOM_VALUE_TEXT) {
            $price = $this->replaceSnippetsInValue($availabilityPriceData['salePriceCV'], $snippetData);

        } else if($availabilityPriceData['salePrice'] == 'useProductSalePrice') {
            $product = wc_get_product($this->entry->ID);
            $price = $product->get_sale_price();

        } else {
            throw new \Exception('Unknown field value '.$availabilityPriceData['salePrice']);
        }

        if($availabilityPriceData['adjustSalePriceValue']) {
            $adjustPriceValue = $this->replaceSnippetsInValue($availabilityPriceData['adjustSalePriceValue'], $snippetData);
            if($availabilityPriceData['adjustSalePriceType'] == '%') {
                $price = $price + $adjustPriceValue * $price / 100;
            } else {
                $price = $price + $adjustPriceValue;
            }
        }

        if($price) {
            return number_format($price, 2) .' '.$availabilityPriceData['currency'];
        } else {
            return "";
        }
    }

    public function getFieldName()
    {
        return 'sale_price';
    }
}