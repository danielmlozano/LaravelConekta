<?php

namespace Danielmlozano\LaravelConekta;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Product implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The produc'ts name
     * @var string
     */
    public $name;

    /**
     * The price per unit, in cents
     * @var integer
     */
    public $unit_price;

    /**
     * The products quantity to be sold
     * @var int
     */
    public $quantity;

    /**
     * Additional information
     *
     * @var array
     */
    public $options;


    /**
     * Create a new Product instance
     *
     * @param string $name
     * @param int $unit_price
     * @param int $quantity
     * @param array $options
     */
    public function __construct(string $name, int $unit_price, int $quantity, array $options = [])
    {
        $this->name = $name;
        $this->unit_price = $unit_price;
        $this->quantity = $quantity;
        $this->options = $options;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            [
                'name' => $this->name,
                'unit_price' => $this->unit_price,
                'quantity' => $this->quantity,
            ],
            $this->options,
        );
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
