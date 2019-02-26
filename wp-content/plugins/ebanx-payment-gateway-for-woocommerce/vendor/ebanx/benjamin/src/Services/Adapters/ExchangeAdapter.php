<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;

class ExchangeAdapter extends BaseAdapter
{
    /**
     * @var string
     */
    private $fromCurrency;

    /**
     * @var string
     */
    private $toCurrency;

    public function __construct($fromCurrency, $toCurrency, Config $config)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        parent::__construct($config);
    }

    public function transform()
    {
        return [
            'integration_key' => $this->getIntegrationKey(),
            'currency_code' => $this->fromCurrency,
            'currency_base_code' => $this->toCurrency,
        ];
    }
}
