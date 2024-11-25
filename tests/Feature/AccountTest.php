<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Customer;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate'); // Ensure database is migrated
        Customer::factory()->create(['id' => 1, 'balance' => 1000]);
        Customer::factory()->create(['id' => 2, 'balance' => 500]);
    }

    #[Test]
    public function it_can_get_customer_balance()
    {
        $response = $this->getJson('/api/v1/accounts/1');

        $response->assertStatus(200)
            ->assertJson(['balance' => 1000]);
    }

    #[Test]
    public function it_returns_404_for_non_existent_customer()
    {
        $response = $this->getJson('/api/v1/accounts/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Customer not found']);
    }

    #[Test]
    public function it_can_deposit_funds_to_customer_account()
    {
        $response = $this->postJson('/api/v1/accounts/1/deposit', ['funds' => 500]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Deposit successful']);

        $this->assertDatabaseHas('customers', [
            'id' => 1,
            'balance' => 1500,
        ]);
    }

    #[Test]
    public function it_can_withdraw_funds_from_customer_account()
    {
        $response = $this->postJson('/api/v1/accounts/1/withdraw', ['funds' => 200]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Withdraw successful']);

        $this->assertDatabaseHas('customers', [
            'id' => 1,
            'balance' => 800,
        ]);
    }

    #[Test]
    public function it_fails_on_negative_deposit_amount()
    {
        $response = $this->postJson('/api/v1/accounts/1/deposit', ['funds' => -100]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => [
                    'headers' => [],
                    'original' => [
                        'error' => 'Invalid input',
                        'details' => [
                            'funds' => [
                                'The funds field must not start with zeros',
                                'The funds must be at least 0.01',
                            ],
                        ],
                    ],
                    'exception' => null,
                ],
            ]);
    }


    #[Test]
    public function it_cannot_withdraw_more_than_balance()
    {
        $response = $this->postJson('/api/v1/accounts/1/withdraw', ['funds' => 2000]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Insufficient balance for withdrawal.']);
    }

    #[Test]
    public function it_can_transfer_funds_between_accounts()
    {
        $response = $this->postJson('/api/v1/accounts/transfer', [
            'from' => 1,
            'to' => 2,
            'funds' => 300,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transfer successful']);

        $this->assertDatabaseHas('customers', [
            'id' => 1,
            'balance' => 700,
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => 2,
            'balance' => 800,
        ]);
    }

    #[Test]
    public function it_prevents_transfer_to_same_account()
    {
        $response = $this->postJson('/api/v1/accounts/transfer', [
            'from' => 1,
            'to' => 1,
            'funds' => 100,
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot transfer funds to the same account.']);
    }
}
