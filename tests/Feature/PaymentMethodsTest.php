<?php

namespace Danielmlozano\LaravelConekta\Tests\Feature;

class PaymentMethodsTest extends FeatureTestCase
{
    public function testPaymentMethodsCanBeCreated()
    {
        $user = $this->createCustomer('customers_can_be_created', true);

        $payment_method = $user->addPaymentMethod("tok_test_amex_8431");
        $id = $payment_method->id;
        $this->assertEquals($id, $user->findPaymentMethod($id)->__get('id'));
    }

    public function testPaymentMethodsCanBeDeleted()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment_method = $user->addPaymentMethod("tok_test_amex_8431");
        $id = $payment_method->id;
        $payment_method_instance = $user->findPaymentMethod($id);

        $user->removePaymentMethod($payment_method_instance);

        $this->assertNull($user->findPaymentMethod($id));
    }
}
