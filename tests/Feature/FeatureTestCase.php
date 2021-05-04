<?php

namespace Danielmlozano\LaravelConekta\Tests\Feature;

use Conekta\Conekta;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Danielmlozano\LaravelConekta\Tests\Fixtures\User;
use Danielmlozano\LaravelConekta\Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Conekta::setApiKey(getenv('CONEKTA_SECRET'));
    }

    protected function setUp(): void
    {
        // Delay consecutive tests to prevent Conekta rate limiting issues.
        sleep(3);

        parent::setUp();

        Eloquent::unguard();

        $this->loadLaravelMigrations();

        $this->artisan('migrate')->run();
    }

    protected function createCustomer($description = 'daniel', $asConektaCustomer = false, $options = []): User
    {
        $user = User::create(array_merge([
            'email' => "{$description}@laravel-conekta-test.com",
            'name' => 'Danielo',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ], $options));

        if ($asConektaCustomer) {
            $user->conekta_id = getenv('CONEKTA_CUSTOMER_ID');
            $user->save();
        }

        return $user;
    }
}
