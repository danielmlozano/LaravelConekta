<?php

namespace Danielmlozano\LaravelConekta\Exceptions;

use Exception;

class CustomerExists extends Exception
{
    /**
     * Create a new CustomerAlreadyCreated instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function exists($owner)
    {
        return new static(class_basename($owner)." is already a Conekta customer with ID {$owner->conekta_id}.");
    }
}
