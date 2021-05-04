<?php

namespace Danielmlozano\LaravelConekta\Exceptions;

use Exception;
use Conekta\PaymentSource;

class InvalidPaymentMethod extends Exception
{
    /**
     * Create a new InvalidPaymentMethod instance.
     *
     * @param  \Conekta\PaymentSource  $paymentMethod
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function invalidOwner(PaymentSource $paymentMethod, $owner)
    {
        return new static(
            "The payment method `{$paymentMethod->id}` does not belong to this customer `$owner->conekta_id`."
        );
    }
}
