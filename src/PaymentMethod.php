<?php

namespace Danielmlozano\LaravelConekta;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Conekta\PaymentSource as ConektaPaymentMethod;
use Danielmlozano\LaravelConekta\Exceptions\InvalidPaymentMethod;

class PaymentMethod implements Arrayable, Jsonable, JsonSerializable
{

     /**
     * The Stripe model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The Stripe PaymentMethod instance.
     *
     * @var \Conekta\PaymentMethod;
     */
    protected $paymentMethod;

    /**
     * Initialize a new PaymentMethod instance
     *
     * @param \Illuminate\Database\Eloquent\Model $owner
     * @param \Conekta\PaymentMethod $paymentMethod
     *
     * @throws \Danielmlozano\LaravelConekta\InvalidPaymentMethod
     */
    public function __construct($owner, ConektaPaymentMethod $paymentMethod)
    {
        if ($owner->conekta_id !== $paymentMethod->parent_id) {
            throw InvalidPaymentMethod::invalidOwner($paymentMethod, $owner);
        }

        $this->owner = $owner;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Return the Conekta payment method object
     *
     * @return \Conekta\PaymentMethod
     */
    public function asConektaPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->__get('id'),
            'type' => $this->__get('type'),
            'created_at' => $this->__get('created_at'),
            'last4' => $this->__get('last4'),
            'name' => $this->__get('name'),
            'exp_month' => $this->__get('exp_month'),
            'exp_year' => $this->__get('exp_year'),
            'brand' => $this->__get('brand'),
            'parent_id' => $this->__get('parent_id'),
        ];
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
        return $this->paymentMethod->{$key};
    }
}
