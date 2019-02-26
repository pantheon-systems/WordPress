<?php
namespace Ebanx\Benjamin\Models;

class Card extends BaseModel
{
    /**
     * If the payment will be captured automatically by EBANX or not.
     *
     * @var boolean
     */
    public $autoCapture;

    /**
     * Generates a token for recurring billing.
     *
     * @var boolean
     */
    public $createToken;

    /**
     * Credit card security code.
     *
     * @var string
     */
    public $cvv;

    /**
     * Credit card due date.
     *
     * @var \DateTime
     */
    public $dueDate;

    /**
     * Credit card cardholder name.
     *
     * @var string
     */
    public $name;

    /**
     * Credit card number.
     *
     * @var string
     */
    public $number;

    /**
     * If a previously created token is informed,
     * no credit card information is needed.
     *
     * @var string
     */
    public $token;

    /**
     * Card brand.
     *
     * @var string
     */
    public $type;
}
