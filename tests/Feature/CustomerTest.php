<?php

namespace Danielmlozano\LaravelConekta\Tests\Feature;

class CustomerTest extends FeatureTestCase
{
    // public function testCustomersCanBeCreated()
    // {
    //     $user = $this->createCustomer('customers_can_be_created');
    //     $user->createAsConektaCustomer(['name'=>$user->name]);

    //     $this->assertTrue($user->hasConektaId());
    // }

    public function testCustomersCanBeUpdated()
    {
        $user = $this->createCustomer('customers_can_be_created', true);

        $user->updateConektaCustomer(['name' => 'Juan Escutia']);

        $this->assertEquals('Juan Escutia', $user->asConektaCustomer()->name);
    }
}
