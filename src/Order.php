<?php

namespace Danielmlozano\LaravelConekta;

use Carbon\Carbon;
use Conekta\Handler;
use Conekta\Order as ConektaOrder;
use Conekta\ParameterValidationError;
use Danielmlozano\LaravelConekta\Exceptions\InvalidProduct;
use Danielmlozano\LaravelConekta\Exceptions\NoPaymentMethod;
use Danielmlozano\LaravelConekta\PaymentMethod;

class Order
{
    /**
     * The currency of the order
     *
     * @var string
     */
    private $currency;

    /**
     * Order's product
     *
     * @var array
     */
    private $products;

    /**
     * The payment method
     *
     * @var \Danielmlozano\LaravelConekta\PaymentMethod
     */
    private $payment_method = null;


    /**
     * The card token in case customer doesn't have payment methods
     *
     * @var string
     */
    private $card_token = null;

    /**
     * Order's owner
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $owner;

    /**
     * Create a new Order instance
     *
     * @param \Illuminate\Database\Eloquent\Model
     */
    public function __construct($owner)
    {
        $this->owner = $owner;
        $this->currency = config('conekta.currency');
        $this->products = [];
    }

    /**
     * Overrides the configuration's currency
     *
     * @param string $currency
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Add a new product to the order
     *
     * @param array|\Danielmlozano\LaravelConekta\Product $product
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function addProduct($product)
    {
        if (is_array($product)) {
            if (!array_key_exists("name", $product, ) || !array_key_exists("unit_price", $product) || !array_key_exists("quantity", $product)) {
                throw InvalidProduct::invalidData();
            }
            $product = new Product(
                $product['name'],
                $product['unit_price'],
                $product['quantity'],
            );
            if (isset($product['options'])) {
                $product->options = $product['options'];
            }
        }

        array_push($this->products, $product);

        return $this;
    }

    /**
     * Return the products in the order
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProducts()
    {
        return collect($this->products);
    }

    /**
     * Set the card token instead of the payment method
     *
     * @param string $card_token
     *
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function withCard(string $card_token)
    {
        $this->card_token = $card_token;
        $this->payment_method = null;
        return $this;
    }

    /**
     * Set the selected payment method to perform the charge
     *
     * @param string|\Danielmlozano\LaravelConekta\PaymentMethod $payment_method
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function withPaymentMethod($payment_method)
    {
        if (is_string($payment_method)) {
            $payment_method = $this->owner->findPaymentMethod($payment_method);
        }
        $this->payment_method = $payment_method;
        return $this;
    }

    /**
     * Set the default payment method to perform the charge
     *
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function withDefaultPaymentMethod()
    {
        $payment_method = $this->owner->getDefaultPaymentMethod();
        if (is_null($payment_method)) {
            throw InvalidPaymentMethod::noDefaultPaymentMethodSetted();
        }
        $this->payment_method = $payment_method;
        return $this;
    }

    /**
     * Prepare the order to be fulfilled with Oxxo Pay
     *
     * @return \Danielmlozano\LaravelConekta\Order;
     */
    public function withOxxoPay()
    {
        $charge = [
            'type' => 'oxxo_cash',
        ];
        $config = config('conekta.oxxo_reference_lifetime');
        if ($config['amount'] > 0) {
            $expiraiton = Carbon::now();
            if ($config['type'] === 'hours') {
                $expiraiton->addDays($config['amount']);
            } else {
                $expiraiton->addHours($config['amount']);
            }
            $charge = array_merge(
                $charge,
                ['expires_at' => $expiraiton->timestamp],
            );
        }
        $this->payment_method = [
            'type' => 'oxxo_cash',
            'object' => $charge,
        ];
        return $this;
    }

    /**
     * Perform the charge
     *
     * @param array $options
     * @return void
     *
     * @throws \Danielmlozano\LaravelConekta\Exceptions\NoPaymentMethod
     */
    public function charge($options = [])
    {
        LaravelConekta::init();
        if (is_array($this->payment_method)) {
            return $this->processWithOxxoPay($options);
        }
        return $this->processWithCard($options);
    }

    /**
     * Process the order to be payed with Oxxo Pay
     *
     * @param array $options
     * @return \Danielmlozano\LaravelConekta\Payment
     */
    public function processWithOxxoPay(array $options)
    {
        try {
            $payment_method = $this->payment_method['object'];
            $amount = array_reduce($this->products, fn ($carry, $item) => $carry += $item->quantity * $item->unit_price);
            $payload = array_merge(
                [
                    'currency' => $this->currency,
                    "customer_info" => [
                        "customer_id" => $this->owner->conekta_id,
                    ],
                    'line_items' => $this->getProducts()->map(
                        fn ($item) => $item->toArray()
                    )->toArray(),
                    'charges' => [
                        [
                            'payment_method' => $payment_method,
                            'amount' => $amount,
                        ]
                    ]
                ],
                $options,
            );

            return new Payment(ConektaOrder::create($payload), 'oxxo_cash');
        } catch (ParameterValidationError $error) {
            echo $error->getMessage();
        } catch (Handler $error) {
            $conekta_error = $error->getConektaMessage();
            \Log::debug('conekta_error->type', [$conekta_error->type]);
            \Log::debug('conekta_error->details', [$conekta_error->details]);
            throw $error;
        }
    }

    /**
     * Process the order with card
     *
     * @param array $options
     * @return \Danielmlozano\LaravelConekta\Payment
     */
    public function processWithCard(array $options)
    {
        if (is_null($this->payment_method) && is_null($this->card_token)) {
            throw NoPaymentMethod::paymentMethodNotSetted();
        }

        $payment_method = [
            'type' => 'card',
        ];

        if (!is_null($this->payment_method)) {
            $payment_method['payment_source_id'] = $this->payment_method->__get('id');
        }

        if (!is_null($this->card_token)) {
            $payment_method['token_id'] = $this->card_token;
        }

        try {
            $amount = array_reduce($this->products, fn ($carry, $item) => $carry += $item->quantity * $item->unit_price);
            $payload = array_merge(
                [
                    'currency' => $this->currency,
                    "customer_info" => [
                        "customer_id" => $this->owner->conekta_id,
                    ],
                    'line_items' => $this->getProducts()->map(
                        fn ($item) => $item->toArray()
                    )->toArray(),
                    'charges' => [
                        [
                            'payment_method' => $payment_method,
                            'amount' => $amount,
                        ]
                    ]
                ],
                $options,
            );

            return new Payment(ConektaOrder::create($payload));
        } catch (Handler $error) {
            $conekta_error = $error->getConektaMessage();
            \Log::debug('conekta_error->type', [$conekta_error->type]);
            \Log::debug('conekta_error->details', [$conekta_error->details]);
            throw $error;
        }
    }
}
