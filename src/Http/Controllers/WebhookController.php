<?php
namespace Danielmlozano\LaravelConekta\Http\Controllers;

use Conekta\Order;
use Danielmlozano\LaravelConekta\Events\ConektaOrderPaid;
use Danielmlozano\LaravelConekta\LaravelConekta;
use Danielmlozano\LaravelConekta\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        $type = $data['type'];

        switch ($type) {
            case "order.paid":
                return $this->processOxxoPayment($data['data']);
                break;
            default:
                return "ok";
        }
    }

    public function processOxxoPayment(array $data)
    {
        LaravelConekta::init();
        $order_id = $data['object']['id'];
        $type = $data['object']['charges']['data'][0]['payment_method']['type'];
        $conekta_order = Order::find($order_id);
        $payment = new Payment($conekta_order, $type);
        ConektaOrderPaid::dispatch($payment);
        \Log::debug('hit');
    }
}
