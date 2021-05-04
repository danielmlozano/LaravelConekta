<?php

namespace Danielmlozano\LaravelConekta;

use Exception;
use Conekta\PaymentMethod;

class InvalidPaymentMethod extends Exception
{
    /**
     * Create a new InvalidPaymentMethod instance.
     *
     * @param  \Conekta\PaymentMethod  $paymentMethod
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function invalidOwner(PaymentMethod $paymentMethod, $owner)
    {
        return new static(
            "The payment method `{$paymentMethod->id}` does not belong to this customer `$owner->stripe_id`."
        );
    }
}
