<?php

namespace Danielmlozano\LaravelConekta\Exceptions;

use Exception;

class InvalidProduct extends Exception
{
    /**
     * Create a new InvalidCustomer instance.
     *
     * @return static
     */
    public static function invalidData()
    {
        return new static('The product data provided is invalid. Make sure you provice an instance of \Danielmlozano\LaravelConekta\Product or an array containing the keys: "name", "unit_price", "quantity" and an optional "options".');
    }
}
