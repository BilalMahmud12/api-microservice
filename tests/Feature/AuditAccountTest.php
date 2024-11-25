<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Customer;
use App\Services\AccountService;

class AuditAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Customer::factory()->count(5)->create();
    }

    #[Test]
    public function it_can_audit_all_accounts_successfully()
    {
        $response = $this->postJson('/api/v1/audit/all');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Audit completed',
            ])
            ->assertJsonStructure([
                'message',
                'results' => [
                    '*' => [
                        'status',
                        'balance'
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_handles_audit_all_accounts_failure()
    {
        $this->mock(AccountService::class, function ($mock) {
            $mock->shouldReceive('auditAllAccounts')->andThrow(new \Exception('Something went wrong'));
        });

        $response = $this->postJson('/api/v1/audit/all');

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Audit failed',
                'details' => 'Something went wrong',
            ]);
    }

    #[Test]
    public function it_can_audit_a_single_account_successfully_with_real_transactions()
    {
        $customer = Customer::factory()->create([
            'balance' => 0,
        ]);

        $this->postJson('/api/v1/accounts/' . $customer->id . '/deposit', ['funds' => 1000])
            ->assertStatus(200)
            ->assertJson(['message' => 'Deposit successful']);

        $this->postJson('/api/v1/accounts/' . $customer->id . '/withdraw', ['funds' => 500])
            ->assertStatus(200)
            ->assertJson(['message' => 'Withdraw successful']);

        $this->postJson('/api/v1/accounts/' . $customer->id . '/deposit', ['funds' => 1500])
            ->assertStatus(200)
            ->assertJson(['message' => 'Deposit successful']);

        $this->postJson('/api/v1/accounts/' . $customer->id . '/withdraw', ['funds' => 700])
            ->assertStatus(200)
            ->assertJson(['message' => 'Withdraw successful']);

        $response = $this->postJson('/api/v1/audit/' . $customer->id);

        $expectedBalance = 1000 - 500 + 1500 - 700; // balance Should be 1300

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Audit completed',
                'customer_id' => $customer->id,
                'balance' => $expectedBalance,
            ]);
    }

    #[Test]
    public function it_handles_audit_failure_for_non_existent_customer()
    {
        $nonExistentCustomerId = 9999;

        $response = $this->postJson('/api/v1/audit/' . $nonExistentCustomerId);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Audit failed for customer',
                'details' => 'Customer not found.',
            ]);
    }

    #[Test]
    public function it_handles_audit_single_account_failure()
    {
        $this->mock(AccountService::class, function ($mock) {
            $mock->shouldReceive('auditAccountBalance')->andThrow(new \Exception('Unexpected error occurred'));
        });

        $customer = Customer::first();

        $response = $this->postJson('/api/v1/audit/' . $customer->id);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Audit failed for customer',
                'details' => 'Unexpected error occurred',
            ]);
    }
}
