<?php

namespace Wpae\App\Service\VariationOptions;

use Wpae\App\Service\Pro\VariationOptions\VariationOptions;
use Wpae\App\Service\VariationOptions\VariationOptions as BasicVariationOptions;

class VariationOptionsFactory
{
    public function createVariationOptions($pmxeEdition)
    {
        switch ($pmxeEdition){
            case 'free':
                return new BasicVariationOptions();
                break;
            case 'paid':
                return new VariationOptions();
                break;
            default:
                throw new \Exception('Unknown PMXE edition');
        }
    }
}