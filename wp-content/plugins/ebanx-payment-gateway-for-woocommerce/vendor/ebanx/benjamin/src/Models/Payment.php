<?php
namespace Ebanx\Benjamin\Models;

class Payment extends BaseModel
{
    /**
     * An Address object.
     *
     * @var Address
     */
    public $address;

    /**
     * Amount to charge in site's currency.
     * @see Ebanx\Benjamin\Models\Configs\Config::$baseCurrency
     *
     * @var float
     */
    public $amountTotal;

    /**
     * Unique ID to identify the customer’s device.
     *
     * @var string
     */
    public $deviceId;

    /**
     * The payment hash Merchant Payment Code (merchant unique ID).
     *
     * @var string
     */
    public $merchantPaymentCode;

    /**
     * A note about the payment.
     *
     * @var string
     */
    public $note = null;

    /**
     * The order number, optional identifier set by the merchant.
     * You can have multiple payments with the same order number.
     *
     * @var string
     */
    public $orderNumber = null;

    /**
     * A Person object.
     *
     * @var Person
     */
    public $person;

    /**
     * An array of Item obejects.
     *
     * @var Item[]
     */
    public $items = [];

    /**
     * Object containing the company’s responsible person information.
     *
     * @var Person
     */
    public $responsible;

    /**
     * The payment method type.
     *
     * @var string
     */
    public $type;

    #EFT Boleto Baloto
    /**
     * Expiry date of the payment.
     * Only applicable to Boleto, Baloto and EFT.
     *
     * @var \DateTime
     */
    public $dueDate = null;

    #EFT TEF
    /**
     * Code for the customer’s bank.
     * Only applicable to EFT.
     *
     * @var string
     */
    public $bankCode = null;

    #CREDIT CARD
    /**
     * Number of instalments.
     * Only applicable to Credit Card.
     *
     * @var int
     */
    public $instalments = null;

    #CARD
    /**
     * A Card object.
     *
     * @var Card
     */
    public $card = null;

    /**
     * Extra information for reports.
     *
     * @var array
     */
    public $userValues = [];

    /**
     * Profile id for risk analysis model
     *
     * @var string
     */
    public $riskProfileId = null;

    #CARD
    /**
     *  Flag to use `Under Review` risk analysis tool.
     */
    public $manualReview = null;
}
