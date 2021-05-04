<?php

namespace Danielmlozano\LaravelConekta\Concerns;

use Danielmlozano\LaravelConekta\Order;

trait PerformCharges
{
    /**
     * Create a new order
     *
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function createOrder()
    {
        return new Order($this);
    }
}
