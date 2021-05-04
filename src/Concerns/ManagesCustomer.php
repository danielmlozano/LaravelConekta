<?php

namespace Danielmlozano\LaravelConekta\Concerns;

use Conekta\Customer;
use Danielmlozano\LaravelConekta\Exceptions\CustomerExists;
use Danielmlozano\LaravelConekta\Exceptions\InvalidCustomer;
use Danielmlozano\LaravelConekta\LaravelConekta;

trait ManagesCustomer
{

    /**
     * Get the Conekta Customer id
     *
     * @return string
     */
    public function conektaId()
    {
        return $this->conekta_id;
    }

    /**
     * Determine if the customer has  a Conekta customer id
     *
     * @return boolean
     */
    public function hasConektaId()
    {
        return !is_null($this->conekta_id);
    }

    /**
     * Create a Conekta customer for the given model
     *
     * @param array $options
     * @return object
     * @throws \Danielmlozano\LaravelConekta\Exceptions\CustomerExists
     */
    public function createAsConektaCustomer(array $options = [])
    {
        if ($this->hasConektaId()) {
            throw CustomerExists::exists($this);
        }

        LaravelConekta::init();

        if (!array_key_exists('email', $options)) {
            $options['email'] = $this->email;
        }

        $customer = Customer::create($options);

        $this->conekta_id = $customer->id;

        $this->save();

        return $customer;
    }

    /**
     * Return the Conekta customer for the model
     *
     * @return object
     */
    public function asConektaCustomer()
    {
        $this->assertCustomerExists();
        LaravelConekta::init();
        return Customer::find($this->conekta_id);
    }

    /**
     * Update the Conekta Customer information
     *
     * @param array $options
     * @return object
     */
    public function updateConektaCustomer(array $options = [])
    {
        $this->assertCustomerExists();
        LaravelConekta::init();
        $customer = $this->asConektaCustomer();
        return $customer->update($options);
    }

    /**
     * Determine if the Conekta customer exists, otherwise, throws an exception
     *
     * @return void
     */
    public function assertCustomerExists()
    {
        if (!$this->hasConektaId()) {
            throw InvalidCustomer::notYetCreated($this);
        }
    }
}
