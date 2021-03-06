<?php

namespace Danielmlozano\LaravelConekta\Concerns;

use Conekta\Handler;
use Danielmlozano\LaravelConekta\PaymentMethod;
use Conekta\PaymentMethod as ConektaPaymentMethod;

trait ManagesPaymentMethods
{
    /**
     * Add a new payment method for the Conekta Customer
     *
     * @param string $token_id
     * @param string $type
     * @return object
     * @throws \Danielmlozano\LaravelConekta\Exceptions\InvalidCustomer
     */
    public function addPaymentMethod($token_id, $type = 'card')
    {
        try {
            $this->assertCustomerExists();
            $customer = $this->asConektaCustomer();
            $payment_method = $customer->createPaymentSource([
                'token_id' => $token_id,
                'type' => $type,
            ]);
            return $payment_method;
        } catch (Handler $error) {
            $conekta_error = $error->getConektaMessage();
            \Log::debug('conekta_error->type', [$conekta_error->type]);
            \Log::debug('conekta_error->details', [$conekta_error->details]);
            throw $error;
        }
    }

    /**
     * Get a collection of the customer's payment methods.
     *
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection|\Danielmlozano\LaravelConekta\PaymentMethod[]
     */
    public function paymentMethods()
    {
        if (!$this->hasConektaId()) {
            return collect();
        }

        $payment_methods = $this->asConektaCustomer()->payment_sources;

        return collect($payment_methods)->map(
            fn ($pm) => new PaymentMethod($this, $pm)
        );
    }

    /**
     * Retrieve the given Payment method by Conekta ID
     *
     * @param string $payment_method
     * @return \Danielmlozano\LaravelConekta\PaymentMethod
     */
    public function findPaymentMethod(string $payment_method)
    {
        if (!$this->hasConektaId()) {
            return null;
        }

        return $this->paymentMethods()->filter(
            fn ($pm) => $pm->__get('id') === $payment_method
        )->first();
    }

    /**
     * Retrieve the default payment method for the model
     *
     * @return \Danielmlozano\LaravelConekta\PaymentMethod
     */
    public function getDefaultPaymentMethod()
    {
        if (!$this->hasConektaId()) {
            return null;
        }

        return $this->paymentMethods()->filter(
            fn ($pm) => $pm->__get('default') === true
        )->first();
    }

    /**
     * Determine if the customer has a default payment method
     *
     * @return bool
     */
    public function hasDefaultPaymentMethod()
    {
        if (!$this->hasConektaId()) {
            return false;
        }

        return (!is_null($this->getDefaultPaymentMethod()));
    }

    /**
     * Remove the given paynt method
     *
     * @param Conekta\PaymentMethod|\Danielmlozano\LaravelConekta\PaymentMethod|String| $payment_method
     * @return mixed
     *
     * @throws \Danielmlozano\LaravelConekta\Exceptions\InvalidCustomer
     */
    public function removePaymentMethod($payment_method)
    {
        $this->assertCustomerExists();

        if (is_string($payment_method)) {
            $payment_method = $this->paymentMethods()->filter(
                fn ($pm) => $pm->_get('id') === $payment_method
            )->first()->asConektaPaymentMethod();
        }

        if ($payment_method instanceof PaymentMethod) {
            $payment_method = $payment_method->asConektaPaymentMethod();
        }

        if ($payment_method->default) {
            $this->owner->card_last_four = null;
            $this->owner->card_brand = null;
            $this->owner->save();
        }

        return $payment_method->delete();
    }

    /**
     * Set the default payment method for the Customer
     *
     * @param PaymentMethod $payment_method
     * @return void
     */
    public function setDefaultPaymentMethod(PaymentMethod $payment_method)
    {
        $this->updateConektaCustomer([
            'default_payment_source_id' => $payment_method->__get('id')
        ]);

        $this->card_brand = $payment_method->__get('brand');
        $this->card_last_four = $payment_method->__get('last4');
        $this->save();
        $this->refresh();
    }
}
