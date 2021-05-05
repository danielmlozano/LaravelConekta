# LaravelConekta

Laravel package that provides a simple, easy-to-use interface to Conekta Payment services.

## Installation

1. First of all, require the Package via Composer

```
$ composer require danielmlozano/laravel-conekta
```

2. Publish the configuration file

```
$ php artisan vendor:publish --tag="conekta-config"
```

3. If necessary, publish the migrations as well

```
$ php artisan vendor:publish --tag="conekta-migrations"
```

4. Add your Conekta public and secret key to your .env file and your currency in ISO format

```
CONEKTA_KEY=key_your_conekta_public_key
CONEKTA_SECRET=key_your_conekta_private_key
CONEKTA_CURRENCY=usd
```

5. If you're using a custom User Model, add it to your .env as well

```
CONEKTA_USER_MODEL="App\Models\CustomModel"
```

6. Add the Purchaser trait to your User model

```
use Danielmlozano\LaravelConekta\Purchaser;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory, Purchaser;
```

7. Now, you can access to the API.

```
$user = User::first();
// Create a new Conekta Customer
$user->createAsConektaCustomer();

//Add a payment method
$user->addPaymentMethod('payment_method_token', 'card');

```

## Documentation

You can read the entire documentation here: <a href="https://danielmlozano.dev/docs/laravelconekta/" target="_blank">https://danielmlozano.dev/docs/laravelconekta/</a>
