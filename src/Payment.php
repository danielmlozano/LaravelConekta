<?php

namespace Danielmlozano\LaravelConekta;

use JsonSerializable;
use Conekta\Order;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Payment implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The Conekta order object
     *
     * @var \Conekta\Order
     */
    protected $conekta_order;

    /**
     * Create a new Payment instance
     *
     * @param Order $conekta_order
     */
    public function __construct(Order $conekta_order)
    {
        $this->conekta_order = $conekta_order;
    }

    /**
     * Return the Conekta Order data
     *
     * @return \Conekta\Order
     */
    public function asConektaOrder()
    {
        return $this->conekta_order;
    }

    /**
     * Return the total of the Payment
     *
     * @return double
     */
    public function total()
    {
        return $this->__get('amount') / 100;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $payment_method = $this->__get('charges')[0]->payment_method;

        $items = $this->__get('line_items');

        $data = [
            'id' => $this->__get('id'),
            'status' => $this->__get('payment_status'),
            'amount' => $this->__get('amount'),
            'auth_code' => $payment_method->auth_code,
            'last4' => $payment_method->last4 ?? null,
            'brand' => $payment_method->brand ?? null,
            'payment_type' => $payment_method->type,
            'order_items' => []
        ];

        foreach ($items as $item) {
            array_push($data['order_items'], [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ]);
        }

        return $data;
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

    /**
     * Dynamically get values from the Conekta Payment Source.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->conekta_order->{$key};
    }
}
