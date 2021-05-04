<?php

namespace Danielmlozano\LaravelConekta;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Conekta\PaymentMethod as ConektaPaymentMethod;

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
        if ($owner->stripe_id !== $paymentMethod->parent_id) {
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
        return $this->asConektaPaymentMethod()->toArray();
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
