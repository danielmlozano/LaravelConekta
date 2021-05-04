<?php

namespace Danielmlozano\LaravelConekta;

use Conekta\Order as ConektaOrder;
use Danielmlozano\LaravelConekta\Exceptions\InvalidProduct;
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
    private $payment_method;

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
     * @return void
     */
    public function withPaymentMethod($payment_method)
    {
        if (is_string($payment_method)) {
            $payment_method = $this->findPaymentMethod($payment_method);
        }
        $this->payment_method = $payment_method;
    }

    public function charge($options = [])
    {
        ConektaOrder::create(array_merge(
            [
                'currency' => $this->currency,
                'customer_id' => $this->owner->conekta_id,
                'line_items' => $this->getProducts()->map(
                    fn ($item) => $item->toArray()
                )->toArray(),
                'charges' => [
                    'payment_method' => $this->payment_method->toArray(),
                ]
            ],
            $options,
        ));
    }
}
