<?php

namespace Danielmlozano\LaravelConekta\Exceptions;

use Exception;

class NoPaymentMethod extends Exception
{
    /**
     * Create a new InvalidCustomer instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function paymentMethodNotSetted()
    {
        return new static('No payment method was setted for the order');
    }
}
