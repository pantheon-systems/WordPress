<?php
namespace Ebanx\Benjamin\Models;

class Item extends BaseModel
{
    /**
     * SKU of the item.
     *
     * @var string
     */
    public $sku = null;

    /**
     * Name of the item.
     *
     * @var string
     */
    public $name = null;

    /**
     * Description of the item.
     *
     * @var string
     */
    public $description = null;

    /**
     * Price of the unity of the item.
     *
     * @var float
     */
    public $unitPrice = null;

    /**
     * Quantity of each item.
     *
     * @var integer
     */
    public $quantity = null;

    /**
     * Type of the item.
     *
     * @var string
     */
    public $type = null;
}
