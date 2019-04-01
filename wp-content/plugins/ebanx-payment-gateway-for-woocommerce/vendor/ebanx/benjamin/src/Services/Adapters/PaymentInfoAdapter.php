<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;

class PaymentInfoAdapter extends BaseAdapter
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $code;

    /**
     * PaymentInfoAdapter constructor.
     *
     * @param string $type
     * @param string $code
     * @param Config $config
     */
    public function __construct($type, $code, Config $config)
    {
        $this->type = $type;
        $this->code = $code;
        parent::__construct($config);
    }

    public function transform()
    {
        return [
            'integration_key' => $this->getIntegrationKey(),
            $this->type => $this->code,
        ];
    }
}
