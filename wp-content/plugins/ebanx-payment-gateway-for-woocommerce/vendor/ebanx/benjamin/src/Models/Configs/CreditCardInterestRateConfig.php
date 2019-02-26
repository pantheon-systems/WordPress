<?php
namespace Ebanx\Benjamin\Models\Configs;

use Ebanx\Benjamin\Models\BaseModel;

class CreditCardInterestRateConfig extends BaseModel
{
    /**
     * Number of instalments
     *
     * @var integer
     */
    public $instalmentNumber;

    /**
     * Interest rate for this number of instalments
     *
     * @var float
     */
    public $interestRate = 0.0;
}
