<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;

class CancelAdapter extends BaseAdapter
{
    /**
     * @var array
     */
    private $hash;

    /**
     * CancelAdapter constructor.
     *
     * @param array $hash
     * @param Config $config
     */
    public function __construct($hash, Config $config)
    {
        $this->hash = $hash;
        parent::__construct($config);
    }

    public function transform()
    {
        return [
            'integration_key' => $this->getIntegrationKey(),
            'hash' => $this->hash,
        ];
    }
}
