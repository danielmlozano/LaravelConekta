<?php

namespace Danielmlozano\LaravelConekta\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Danielmlozano\LaravelConekta\Events\ConektaOrderPaid;

class EventsTest extends FeatureTestCase
{
    public function testOrderPaidEventIsDispatched()
    {
        $user = $this->createCustomer('customers_can_be_created', true);

        $data = array(
            'type' => 'order.paid',
            'data' => array(
                'object' =>
                array(
                  'livemode' => false,
                  'amount' => 10000,
                  'currency' => 'USD',
                  'payment_status' => 'paid',
                  'amount_refunded' => 0,
                  'customer_info' =>
                  array(
                    'email' => 'customers_can_be_created@laravel-conekta-test.com',
                    'name' => 'Juan Escutia',
                    'corporate' => false,
                    'customer_id' => 'cus_2phNrLnZ7xuquMqiZ',
                    'object' => 'customer_info',
                  ),
                  'object' => 'order',
                  'id' => 'ord_2pkJFBz4BRGBDkdrC',
                  'metadata' =>
                  array(
                  ),
                  'created_at' => 1620937261,
                  'updated_at' => 1620937293,
                  'line_items' =>
                  array(
                    'object' => 'list',
                    'has_more' => false,
                    'total' => 1,
                    'data' =>
                    array(
                      0 =>
                      array(
                        'name' => 'Test',
                        'unit_price' => 10000,
                        'quantity' => 1,
                        'object' => 'line_item',
                        'id' => 'line_item_2pkJFBz4BRGBDkdrA',
                        'parent_id' => 'ord_2pkJFBz4BRGBDkdrC',
                        'metadata' =>
                        array(
                        ),
                        'antifraud_info' =>
                        array(
                        ),
                      ),
                    ),
                  ),
                  'charges' =>
                  array(
                    'object' => 'list',
                    'has_more' => false,
                    'total' => 1,
                    'data' =>
                    array(
                      0 =>
                      array(
                        'id' => '609d8a2d41de27116ab6e3b3',
                        'livemode' => false,
                        'created_at' => 1620937261,
                        'currency' => 'MXN',
                        'payment_method' =>
                        array(
                          'service_name' => 'OxxoPay',
                          'barcode_url' => 'https://s3.amazonaws.com/cash_payment_barcodes/sandbox_reference.png',
                          'object' => 'cash_payment',
                          'type' => 'oxxo',
                          'expires_at' => 1623542400,
                          'store_name' => 'OXXO',
                          'reference' => '98000010843400',
                        ),
                        'object' => 'charge',
                        'description' => 'Payment from order',
                        'status' => 'paid',
                        'amount' => 200940,
                        'paid_at' => 1620937293,
                        'fee' => 9091,
                        'customer_id' => null,
                        'order_id' => 'ord_2pkJFBz4BRGBDkdrC',
                      ),
                    ),
                  ),
                ),
                'previous_attributes' =>
                array(
                ),
            )
        );
        Event::fake();


        $response = $this->post('/conekta/webhook', $data);
        $response->assertStatus(200);


        Event::assertDispatched(
            ConektaOrderPaid::class
        );
    }
}
