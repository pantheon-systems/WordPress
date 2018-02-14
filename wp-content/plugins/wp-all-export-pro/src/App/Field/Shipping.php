<?php

namespace Wpae\App\Field;


class Shipping extends Field
{
    const SECTION = 'shipping';

    public function getValue($snippetData)
    {
        $shippingData = $this->feed->getSectionFeedData(self::SECTION);

        if(isset($shippingData['shippingPrice'])) {

            $price = $this->replaceSnippetsInValue($shippingData['shippingPrice'], $snippetData);
            $adjustShippingPriceValue = $this->replaceSnippetsInValue($shippingData['adjustShippingPriceValue'], $snippetData);

            if($shippingData['adjustShippingPrice'] && $shippingData['adjustPriceType'] == '%') {
                $price = $price * $adjustShippingPriceValue/100;
            } else if($shippingData['adjustShippingPrice'] && $shippingData['adjustPriceType'] == 'USD') {
                $price = $price + $adjustShippingPriceValue;
            }

            return $this->formatPrice($price);
        } else {
            return '';
        }
    }

    public function getFieldName()
    {
        return 'shipping';
    }

    private function formatPrice($price)
    {
        $availabilityPriceData = $this->feed->getSectionFeedData('availabilityPrice');

        return ':::'.number_format($price,2).''.$availabilityPriceData['currency'];
    }
}