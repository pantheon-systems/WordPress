<?php

function pmxe_prepare_price( $price, $disable_prepare_price, $prepare_price_to_woo_format, $convert_decimal_separator ){

    if ( $disable_prepare_price ){
        $price = preg_replace("/[^0-9\.,]/","", $price);
    }
    if ( $convert_decimal_separator and strlen($price) > 3)
    {
        $decimal_sep    = get_option( 'woocommerce_price_decimal_sep', '.' );
        $thousand_sep   = get_option( 'woocommerce_price_thousand_sep', ',' );
        $comma_position = strrpos($price, ",", strlen($price) - 3);
        if ($comma_position !== false)
        {
            $price = str_replace(".", "", $price);
            $comma_position = strrpos($price, ",");
            $price = str_replace(",", "", substr_replace($price, ".", $comma_position, 1));
        }
        else
        {
            $comma_position = strrpos($price, ".", strlen($price) - 3);
            if ($comma_position !== false)
            {
                $price = str_replace(",", "", $price);
            }
            elseif(strlen($price) > 4)
            {
                $comma_position = strrpos($price, ",", strlen($price) - 4);

                if ($comma_position and strlen($price) - $comma_position == 4)
                {
                    $price = str_replace(",", "", $price);
                }
                else
                {
                    $comma_position = strrpos($price, ".", strlen($price) - 4);

                    if ($comma_position and strlen($price) - $comma_position == 4)
                    {
                        $price = str_replace(".", "", $price);
                    }
                }
            }
        }
    }
    if ( $prepare_price_to_woo_format ){
        $price = str_replace(",", ".", $price);
        $price = str_replace(",", ".", str_replace(".", "", preg_replace("%\.([0-9]){1,2}?$%", ",$0", $price)));

        $price = ("" != $price) ? number_format( (double) $price, 2, '.', '' ) : "";
    }
    
    return apply_filters('pmxe_price', $price);
}