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
}
