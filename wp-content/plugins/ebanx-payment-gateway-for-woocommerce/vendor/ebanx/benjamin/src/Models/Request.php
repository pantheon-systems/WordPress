<?php
namespace Ebanx\Benjamin\Models;

class Request extends BaseModel
{
    /**
     * Amount to charge in site's currency.
     * @see Ebanx\Benjamin\Models\Configs\Config::$baseCurrency
     *
     * @var float
     */
    public $amount;

    /**
     * Your internal payment unique identification.
     *
     * @var string
     */
    public $merchantPaymentCode;

    /**
     * Order number visible to customer.
     *
     * @var string
     */
    public $orderNumber = '';

    /**
     * Allowed payment methods filter string.
     * Do not change unless you know exactly what you are doing.
     *
     * @var string
     */
    public $type = '_all';

    /**
     * Max allowed instalments.
     * Only applicable to Credit Card.
     *
     * @var int
     */
    public $maxInstalments = 12;

    /**
     * Min allowed instalments.
     * Only applicable to Credit Card.
     *
     * @var int
     */
    public $minInstalments = 1;

    /**
     * Wether or not if EBANX Checkout page will skip the page displaying
     * payment status and redirect back imediately.
     */
    public $skipThankyouPage = false;

    /**
     * Expiry date of the payment.
     * Only applicable to Boleto, Baloto and EFT.
     *
     * @var \DateTime
     */
    public $dueDate = null;

    /**
     * Extra information for reports.
     *
     * @var array
     */
    public $userValues = [];

    /**
     * A Person object.
     *
     * @var Person
     */
    public $person = null;

    /**
     * An Address object.
     *
     * @var Address
     */
    public $address = null;

    /**
     * A SubAccount object.
     *
     * @var SubAccount
     */
    public $subAccount = null;

    /**
     * Redirect URL after checkout page
     * @var string
     */
    public $redirectUrl = null;

    /**
     * Whether or not a payment will be submitted to manual review
     *
     * @var bool
     */
    public $manualReview = false;
}
