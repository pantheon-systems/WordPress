<?php

namespace Wpae\App\Field;


class Price extends Field
{
    const SECTION = 'availabilityPrice';

    public function getValue($snippetData)
    {
        $availabilityPriceData = $this->feed->getSectionFeedData(self::SECTION);

        if($availabilityPriceData['price'] == 'useProductPrice') {
            $product = wc_get_product( $this->entry->ID );
            $price = $product->get_regular_price();
        }
        else if($availabilityPriceData['price'] == self::CUSTOM_VALUE_TEXT) {
            $price = $this->replaceSnippetsInValue($availabilityPriceData['priceCV'], $snippetData);
        
        } else {
            throw new \Exception('Unknown field value');
        }

        if($availabilityPriceData['adjustPriceValue']) {
            $adjustPriceValue = $this->replaceSnippetsInValue($availabilityPriceData['adjustPriceValue'], $snippetData);
            if($availabilityPriceData['adjustPriceType'] == '%') {
                $price = $adjustPriceValue/100*$price;
            } else {
                $price = $price + $adjustPriceValue;
            }
        }

        if($price) {
            if(is_numeric($price)){
                return number_format($price, 2) .' '.$availabilityPriceData['currency'];
            } else {
                return $price.' '.$availabilityPriceData['currency'];
            }

        } else {
            return "";
        }

    }

    public function getFieldName()
    {
        return 'price';
    }
}