<?php
namespace Ebanx\Benjamin\Models;

class Address extends BaseModel
{
    /**
     * Customer address (street name).
     *
     * @var string
     */
    public $address;

    /**
     * Customer city.
     *
     * @var string
     */
    public $city;

    /**
     * Customer country.
     *
     * @var string
     */
    public $country;

    /**
     * Customer state.
     *
     * @var string
     */
    public $state;

    /**
     * Extra address field for complimentary data.
     *
     * @var string
     */
    public $streetComplement;

    /**
     * Customer street number.
     *
     * @var string
     */
    public $streetNumber;

    /**
     * Customer’s zipcode.
     *
     * @var string
     */
    public $zipcode;
}
