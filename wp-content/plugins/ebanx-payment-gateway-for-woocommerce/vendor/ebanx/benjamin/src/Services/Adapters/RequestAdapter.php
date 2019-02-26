<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\SubAccount;
use Ebanx\Benjamin\Models\Request;
use Ebanx\Benjamin\Models\Configs\Config;

class RequestAdapter extends BaseAdapter
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;
        parent::__construct($config);
    }

    /**
     * @return object
     */
    public function transform()
    {
        $result = [
            'integration_key' => $this->getIntegrationKey(),
            'currency_code' => $this->config->baseCurrency,
            'amount' => $this->request->amount,
            'merchant_payment_code' => $this->request->merchantPaymentCode,
            'order_number' => $this->request->orderNumber,
            'payment_type_code' => $this->request->type,
            'bypass_boleto_screen' => $this->request->skipThankyouPage,
            'due_date' => $this->transformDate($this->request->dueDate),
            'notification_url' => $this->getNotificationUrl(),
            'redirect_url' => $this->request->redirectUrl,
            'manual_review' => $this->request->manualReview,
            'instalments' => implode('-', [
                $this->request->minInstalments,
                $this->request->maxInstalments,
            ]),
        ];

        $result = array_replace($result, $this->transformPerson($this->request->person));
        $result = array_replace($result, $this->transformAddress($this->request->address));
        $result = array_replace($result, $this->transformUserValues($this->request->userValues));
        $result = array_replace($result, $this->transformSubAccount($this->request->subAccount));

        return (object) $result;
    }

    protected function transformDate(\DateTime $date = null)
    {
        return isset($date)
            ? $date->format('d/m/Y')
            : null;
    }

    protected function transformPerson(Person $person = null)
    {
        return [
            'name' => $person->name,
            'email' => $person->email,
            'phone_number' => $person->phoneNumber,
            'person_type' => $person->type,
            'birth_date' => $person->birthdate,
        ];
    }

    protected function transformAddress(Address $address = null)
    {
        return [
            'country' => Country::toIso($address->country),
            'zipcode' => $address->zipcode,
            'address' => $address->address,
            'street_number' => $address->streetNumber,
            'street_complement' => $address->streetComplement,
            'city' => $address->city,
            'state' => $address->state,
        ];
    }

    protected function transformUserValues(array $userValues = null)
    {
        $userValues = array_replace(
            $userValues,
            $this->config->userValues,
            [5 => 'Benjamin']
        );

        $result = [];

        for ($i = 1; $i <= 5; $i++) {
            if (!isset($userValues[$i])) {
                continue;
            }

            $result['user_value_' . $i] = $userValues[$i];
        }

        return $result;
    }

    protected function transformSubAccount(SubAccount $subAccount = null)
    {
        if (!$subAccount) {
            return [];
        }

        return [
            'sub_acc_name' => $this->request->subAccount->name,
            'sub_acc_image_url' => $this->request->subAccount->imageUrl,
        ];
    }
}
