<?php
namespace Ebanx\Benjamin\Models;

class Person extends BaseModel
{
    const TYPE_PERSONAL = 'personal';
    const TYPE_BUSINESS = 'business';

    const DOCUMENT_TYPES_ARGENTINA_CUIT = 'ARG_CUIT';
    const DOCUMENT_TYPE_ARGENTINA_CUIL = 'ARG_CUIL';
    const DOCUMENT_TYPE_ARGENTINA_CDI = 'ARG_CDI';
    const DOCUMENT_TYPE_COLOMBIA_CC = 'COL_CC';
    const DOCUMENT_TYPE_COLOMBIA_NIT = 'COL_NIT';

    /**
     * The type of customer.
     * Supported person types: 'personal' and 'business'.
     *
     * @var string
     */
    public $type = self::TYPE_PERSONAL;

    /**
     * Customers birthdate.
     *
     * @var \DateTime
     */
    public $birthdate;

    /**
     * Customers document.
     *
     * @var string
     */
    public $document;

    /**
     * The type of customer's document.
     * Supported document types: 'ARG_CUIT', 'ARG_CUIL', 'ARG_CDI', 'COL_CC' and 'COL_NIT'
     *
     * @var string
     */
    public $documentType = null;

    /**
     * Customers email.
     *
     * @var string
     */
    public $email;

    /**
     * Customers IP.
     *
     * @var string
     */
    public $ip;

    /**
     * Customers name.
     *
     * @var string
     */
    public $name;

    /**
     * Customers phone number.
     *
     * @var string
     */
    public $phoneNumber;
}
