<?php
namespace Ebanx\Benjamin;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;
use Ebanx\Benjamin\Models\Configs\AddableConfig;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\CancelPayment;
use Ebanx\Benjamin\Services\Gateways;
use Ebanx\Benjamin\Services\PaymentInfo;
use Ebanx\Benjamin\Services\Exchange;
use Ebanx\Benjamin\Services\Refund;
use Ebanx\Benjamin\Services\Http\Client as HttpClient;

class Facade
{
    const VERSION="1.18.0";
    /**
     * Mock this in your tests extending and using ClientForTests
     * and any Engine you like (we provide EchoEngine)
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CreditCardConfig
     */
    protected $creditCardConfig;

    /**
     * @param AddableConfig $config,... Configuration objects
     * @return Facade
     */
    public function addConfig(AddableConfig $config)
    {
        $args = func_get_args();
        foreach ($args as $config) {
            $class = $config->getShortClassName();
            call_user_func([$this, 'with'.$class], $config);
        }

        return $this;
    }

    /**
     * @param Config $config
     * @return Facade
     */
    public function withConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param CreditCardConfig $creditCardConfig
     * @return Facade
     */
    public function withCreditCardConfig(CreditCardConfig $creditCardConfig)
    {
        $this->creditCardConfig = $creditCardConfig;
        return $this;
    }

    /**
     * @param Payment $payment
     * @return array
     * @throws \InvalidArgumentException
     */
    public function create(Payment $payment)
    {
        if ($payment->type === null) {
            throw new \InvalidArgumentException('Invalid payment type');
        }

        if (!method_exists($this, $payment->type)) {
            throw new \InvalidArgumentException('Invalid payment type');
        }

        $instance = call_user_func([$this, $payment->type]);
        return $instance->create($payment);
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getTicketHtml($hash)
    {
        $info = $this->paymentInfo()->findByHash($hash);

        $gatewayName = $this->getGatewayNameFromType($info['payment']['payment_type_code']);
        if (!$gatewayName) {
            return null;
        }

        $gateway = $this->{$gatewayName}();
        if (!method_exists($gateway, 'getTicketHtml')) {
            return null;
        }

        return $gateway->getTicketHtml($hash);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isValidPrivateKey($key)
    {
        $data = ['integration_key' => $key];
        $response = $this->getHttpClient()->validatePrivateKey($data);

        return $response['status'] === 'SUCCESS';
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws \Exception
     */
    public function isValidPublicKey($key)
    {
        $data = ['public_integration_key' => $key];
        try {
            $response = $this->getHttpClient()->validatePublicKey($data);

            return $response['status'] === 'SUCCESS';
        } catch (\Exception $e) {
            if ($e->getCode() === 409) {
                return false;
            }

            throw $e;
        }
    }

    # Gateways

    /**
     * @return Gateways\BankTransfer
     */
    public function banktransfer()
    {
        return new Gateways\BankTransfer($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Baloto
     */
    public function baloto()
    {
        return new Gateways\Baloto($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Boleto
     */
    public function boleto()
    {
        return new Gateways\Boleto($this->config, $this->getHttpClient());
    }

    /**
     * @param  CreditCardConfig $creditCardConfig (optional) credit card config
     * @return Gateways\CreditCard
     */
    public function creditCard(CreditCardConfig $creditCardConfig = null)
    {
        if ($creditCardConfig === null) {
            $creditCardConfig = $this->creditCardConfig;
        }

        return new Gateways\CreditCard($this->config, $creditCardConfig, $this->getHttpClient());
    }

    /**
     * @return Gateways\Oxxo
     */
    public function oxxo()
    {
        return new Gateways\Oxxo($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Spei
     */
    public function spei()
    {
        return new Gateways\Spei($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Sencillito
     */
    public function sencillito()
    {
        return new Gateways\Sencillito($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Webpay
     */
    public function webpay()
    {
        return new Gateways\Webpay($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Multicaja
     */
    public function multicaja()
    {
        return new Gateways\Multicaja($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\PagoEfectivo
     */
    public function pagoEfectivo()
    {
        return new Gateways\PagoEfectivo($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Tef
     */
    public function tef()
    {
        return new Gateways\Tef($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\EbanxAccount
     */
    public function ebanxAccount()
    {
        return new Gateways\EbanxAccount($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Eft
     */
    public function eft()
    {
        return new Gateways\Eft($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Servipag
     */
    public function servipag()
    {
        return new Gateways\Servipag($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\DebitCard
     */
    public function debitCard()
    {
        return new Gateways\DebitCard($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\SafetyPayCash
     */
    public function safetyPayCash()
    {
        return new Gateways\SafetyPayCash($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\SafetyPayOnline
     */
    public function safetyPayOnline()
    {
        return new Gateways\SafetyPayOnline($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Rapipago
     */
    public function rapipago()
    {
        return new Gateways\Rapipago($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Pagofacil
     */
    public function pagofacil()
    {
        return new Gateways\Pagofacil($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\OtrosCupones
     */
    public function otrosCupones()
    {
        return new Gateways\OtrosCupones($this->config, $this->getHttpClient());
    }

    /**
     * @return Gateways\Hosted
     */
    public function hosted()
    {
        return new Gateways\Hosted($this->config, $this->getHttpClient());
    }

    /**
     * @return PaymentInfo
     */
    public function paymentInfo()
    {
        return new PaymentInfo($this->config, $this->getHttpClient());
    }

    /**
     * @return Exchange
     */
    public function exchange()
    {
        return new Exchange($this->config, $this->getHttpClient());
    }

    /**
     * @return Refund
     */
    public function refund()
    {
        return new Refund($this->config, $this->getHttpClient());
    }

    /**
     * @return CancelPayment
     */
    public function cancelPayment()
    {
        return new CancelPayment($this->config, $this->getHttpClient());
    }

    public function setSource($service, $version)
    {
        $this->getHttpClient()->addUserAgentInfo($service . '/' . $version);
    }

    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
            $this->httpClient->switchMode($this->config->isSandbox);
        }
        return $this->httpClient;
    }

    protected function getGatewayNameFromType($apiType)
    {
        foreach ($this->getAllPublicServices() as $method => $service) {
            $class = get_class($service);

            if (!defined($class.'::API_TYPE')) {
                continue;
            }

            if ($class::API_TYPE !== $apiType) {
                continue;
            }

            return $method;
        }

        return null;
    }

    protected function getAllPublicServices()
    {
        $methods = get_class_methods(get_class($this));
        $services = [];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod($this, $method);

            if (!$reflection->isPublic()
                || $reflection->getNumberOfRequiredParameters() > 0) {
                continue;
            }

            $services[$method] = $this->{$method}();
        }

        return $services;
    }
}
