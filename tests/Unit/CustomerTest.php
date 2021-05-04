<?php

namespace Danielmlozano\LaravelConekta\Tests\Unit;

use Danielmlozano\LaravelConekta\Exceptions\InvalidCustomer;
use Danielmlozano\LaravelConekta\Tests\TestCase;
use Danielmlozano\LaravelConekta\Tests\Fixtures\User;

class CustomerTest extends TestCase
{
    public function testConektaCustomerThrowsExceptionWhenConektaIdIsNull()
    {
        $user = new User();

        $this->expectException(InvalidCustomer::class);

        $user->asConektaCustomer();
    }
}
