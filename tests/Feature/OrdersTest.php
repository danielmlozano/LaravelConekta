<?php

namespace Danielmlozano\LaravelConekta\Tests\Feature;

use Danielmlozano\LaravelConekta\Product;
use Danielmlozano\LaravelConekta\Payment;

class OrdersTest extends FeatureTestCase
{
    public function testOrdersCanBeCreatedAndPerformCharges()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment_method = $user->paymentMethods()->first();
        if (!$payment_method) {
            $user->addPaymentMethod("tok_test_amex_8431");
            $payment_method = $user->paymentMethods()->first();
        }
        $order = $user->createOrder();
        $order->addProduct(new Product('Test', 10000, 1));
        $order->withPaymentMethod($payment_method->id);
        $payment = $order->charge();
        $this->assertNotNull($payment->__get('id'));
        $this->assertEquals(100, $payment->total());
    }

    public function testOrdersCanBeCreatedAndPerformChargeWithDefaultPaymentMethod()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment_method = $user->paymentMethods()->first();
        if (!$payment_method) {
            $user->addPaymentMethod("tok_test_amex_8431");
            $payment_method = $user->paymentMethods()->first();
        }

        $user->setDefaultPaymentMethod($payment_method);

        $payment = $user->createOrder()
            ->addProduct(new Product('Test', 10000, 1))
            ->withDefaultPaymentMethod()
            ->charge();

        $this->assertNotNull($payment->__get('id'));
        $this->assertEquals(100, $payment->total());
    }

    public function testOrdersCanBeCreatedAndPerformOneOffCharges()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment_method = $user->paymentMethods()->first();
        if (!$payment_method) {
            $user->addPaymentMethod("tok_test_amex_8431");
            $payment_method = $user->paymentMethods()->first();
        }

        $user->setDefaultPaymentMethod($payment_method);

        $payment = $user->createOrder()
            ->addProduct(new Product('Test', 10000, 1))
            ->withCard('tok_test_visa_4242')
            ->charge();

        $this->assertNotNull($payment->__get('id'));
        $this->assertEquals(100, $payment->total());
    }

    public function testOrdersCanBeCreatedWithOxxoPay()
    {
        $user = $this->createCustomer('customers_can_be_created', true);
        $payment = $user->createOrder()
            ->addProduct(new Product('Test', 10000, 1))
            ->withOxxoPay()
            ->charge();
        $this->assertNotNull($payment->__get('id'));
        $this->assertNotNull($payment->__get('charges')[0]->payment_method->reference);
        $this->assertEquals(14, strlen($payment->__get('charges')[0]->payment_method->reference));
    }
}
