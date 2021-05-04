<?php

namespace Danielmlozano\LaravelConekta\Concerns;

use Danielmlozano\LaravelConekta\Order;

trait PerformCharges
{
    public function createOrder()
    {
        return new Order($this);
    }
}
