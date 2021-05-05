<?php

namespace Danielmlozano\LaravelConekta;

use Conekta\Handler;
use Conekta\Order as ConektaOrder;
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
     * Perform the charge
     *
     * @param array $options
     * @return void
     *
     * @throws \Danielmlozano\LaravelConekta\Exceptions\NoPaymentMethod
     */
    public function charge($options = [])
    {
        if (is_null($this->payment_method)) {
            throw NoPaymentMethod::paymentMethodNotSetted();
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
                            'payment_method' => [
                                'type' => 'card',
                                'payment_source_id' => $this->payment_method->__get('id'),
                            ],
                            'amount' => $amount,
                        ]
                    ]
                ],
                $options,
            );

            return new Payment(ConektaOrder::create($payload));
        } catch (Handler $error) {
            // $conekta_error = $error->getConektaMessage();
            // print(var_dump($conekta_error->type));
            // print(var_dump($conekta_error->details));
            throw $error;
        }
    }
}
