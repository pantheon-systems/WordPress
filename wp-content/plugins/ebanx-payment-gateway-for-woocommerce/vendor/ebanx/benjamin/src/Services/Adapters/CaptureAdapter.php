<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;

class CaptureAdapter extends BaseAdapter
{
    /**
     * @var array
     */
    private $data;

    /**
     * RefundAdapter constructor.
     *
     * @param array $hash
     * @param Config $config
     */
    public function __construct($hash, Config $config)
    {
        $this->data = $hash;
        parent::__construct($config);
    }

    public function transform()
    {
        $transformed = [
            'integration_key' => $this->getIntegrationKey(),
            'merchant_capture_code' => $this->data['merchantCaptureCode'],
            'amount' => $this->data['amount'],
        ];
        if (isset($this->data['hash'])) {
            $transformed['hash'] = $this->data['hash'];
        }
        if (isset($this->data['merchantPaymentCode'])) {
            $transformed['merchant_payment_code'] = $this->data['merchantPaymentCode'];
        }

        return $transformed;
    }
}
