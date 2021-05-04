<?php

namespace Danielmlozano\LaravelConekta;

use Danielmlozano\LaravelConekta\Concerns\ManagesCustomer;
use Danielmlozano\LaravelConekta\Concerns\ManagesPaymentMethods;
use Danielmlozano\LaravelConekta\Concerns\PerformCharges;

trait Purchaser
{
    use ManagesCustomer;
    use ManagesPaymentMethods;
    use PerformCharges;
}
