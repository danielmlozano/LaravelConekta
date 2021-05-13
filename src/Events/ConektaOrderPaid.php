<?php

namespace Danielmlozano\LaravelConekta\Events;

use Danielmlozano\LaravelConekta\Payment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ConektaOrderPaid
{
    use Dispatchable, SerializesModels;

    /**
     * The Order
     *
     * @var \Danielmlozano\LaravelConekta\Payment
     */
    public $order;

    public function __construct(Payment $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
