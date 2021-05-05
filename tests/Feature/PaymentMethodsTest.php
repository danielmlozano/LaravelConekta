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

    public function testPaymentMethodCanBeSettedAsDefault()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment_method = $user->paymentMethods()->first();
        if (!$payment_method) {
            $user->addPaymentMethod("tok_test_amex_8431");
            $payment_method = $user->paymentMethods()->first();
        }

        $user->setDefaultPaymentMethod($payment_method);

        $this->assertEquals($payment_method->__get('id'), $user->getDefaultPaymentMethod()->__get('id'));
        $this->assertEquals($user->card_brand, $payment_method->__get('brand'));
        $this->assertEquals($user->card_last_four, $payment_method->__get('last4'));
    }
}
