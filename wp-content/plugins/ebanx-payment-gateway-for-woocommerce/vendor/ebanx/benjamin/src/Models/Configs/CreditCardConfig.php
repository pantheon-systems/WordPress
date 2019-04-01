<?php
namespace Ebanx\Benjamin\Models\Configs;

use Ebanx\Benjamin\Models\BaseModel;
use Ebanx\Benjamin\Models\Currency;

class CreditCardConfig extends BaseModel implements AddableConfig
{
    const MAX_INSTALMENTS = 36;

    /**
     * Number of max instalments, defaults to 12.
     *
     * @var integer
     */
    public $maxInstalments = self::MAX_INSTALMENTS;

    /**
     * Minimum instalment amount.
     * Default varies by currency.
     *
     * @var float
     */
    public $minInstalmentAmount = 0.0;

    /**
     * List of interest rate config objects.
     *
     * @var array
     */
    public $interestRates = [];

    public static function acquirerMinInstalmentValueForCurrency($currency)
    {
        $relation = [
            Currency::BRL => 5,
            Currency::MXN => 100,
            Currency::COP => 1,
            Currency::ARS => 1,
        ];

        return isset($relation[$currency])
            ? $relation[$currency]
            : null;
    }

    /**
     * Adds an interest rate config object for the credit card config.
     *
     * @param integer $instalmentNumber The instalment number for this rate configuration
     * @param float   $rate              The interest rate to be applied
     * @return CreditCardConfig itself
     */
    public function addInterest($instalmentNumber, $rate)
    {
        $this->interestRates[] = new CreditCardInterestRateConfig([
            'instalmentNumber' => $instalmentNumber,
            'interestRate' => $rate,
        ]);

        return $this;
    }
}
