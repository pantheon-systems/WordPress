<?php

namespace Wpae\App\Field;


class ShippingWeight extends Field
{
    const SECTION = 'shipping';

    public function getValue($snippetData)
    {
        $shippingData = $this->feed->getSectionFeedData(self::SECTION);

        if($shippingData['weight'] == 'useWooCommerceProductValues') {

            $product = wc_get_product($this->entry->ID);
            $weightUnit = get_option('woocommerce_weight_unit');
            $weight = $product->get_weight();

            if($weight) {
                return $weight . ' ' . $weightUnit;
            } else {
                return '';
            }

        } else {
            return $this->replaceSnippetsInValue($shippingData['weightCV'], $snippetData);
        }
    }

    public function getFieldName()
    {
        return 'shipping_weight';
    }
}