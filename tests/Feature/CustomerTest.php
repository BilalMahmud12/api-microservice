<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_all_customers_with_default_pagination()
    {
        Customer::factory()->count(15)->create();

        $response = $this->get('/api/v1/customers');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'name', 'surname', 'balance', 'created_at', 'updated_at']
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => ['url', 'label', 'active']
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

        $responseData = $response->json();
        $this->assertEquals(1, $responseData['current_page']);
        $this->assertEquals(15, $responseData['total']);
    }

    #[Test]
    public function it_gets_all_customers_with_custom_pagination()
    {
        Customer::factory()->count(50)->create();

        $response = $this->get('/api/v1/customers?page=2&per_page=5');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'surname', 'balance', 'created_at', 'updated_at']
            ]
        ]);

        $response->assertJson([
            'current_page' => 2,
            'per_page' => 5,
            'total' => 50,
        ]);
    }

    #[Test]
    public function it_gets_all_customers_with_invalid_pagination()
    {
        $response = $this->get('/api/v1/customers?page=abc&per_page=-5');
        $response->assertStatus(422);
        $response->assertJson([
            'page' => ['The page param must be a positive integer greater than zero.'],
            'per_page' => ['The per_page param must be a positive integer greater than zero.'],
        ]);
    }

    #[Test]
    public function it_gets_all_customers_on_empty_database()
    {
        $response = $this->get('/api/v1/customers');
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    #[Test]
    public function it_should_create_customer()
    {
        $response = $this->post('/api/v1/customers', [
            'name' => 'John',
            'surname' => 'Doe'
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'John',
            'surname' => 'Doe',
            'balance' => 0
        ]);
    }

    #[Test]
    public function it_should_fail_on_create_customer_with_missing_name()
    {
        $response = $this->post('/api/v1/customers', [
            'surname' => 'Doe'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'name' => ['The name field is required.']
        ]);
    }

    #[Test]
    public function it_should_fail_on_create_customer_with_balance_field()
    {
        $response = $this->post('/api/v1/customers', [
            'name' => 'John',
            'surname' => 'Doe',
            'balance' => 100
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'balance' => ['The balance field is prohibited.']
        ]);
    }

    #[Test]
    public function it_gets_customer_by_id()
    {
        $customer = Customer::factory()->create();

        $response = $this->get('/api/v1/customers/' . $customer->id);
        $response->assertStatus(200);
        $response->assertJson([
            'name' => $customer->name,
            'surname' => $customer->surname,
            'balance' => $customer->balance
        ]);
    }

    #[Test]
    public function it_gets_customer_by_non_existing_id()
    {
        $response = $this->get('/api/v1/customers/99999');
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Customer not found']);
    }

    #[Test]
    public function it_updates_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->put('/api/v1/customers/' . $customer->id, [
            'name' => 'Jane',
            'surname' => 'Doe'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Jane',
            'surname' => 'Doe'
        ]);
    }

    #[Test]
    public function it_fails_on_update_customer_balance()
    {
        $customer = Customer::factory()->create();

        $response = $this->put('/api/v1/customers/' . $customer->id, [
            'balance' => 100
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'balance' => ['The balance field is prohibited.']
        ]);
    }

    #[Test]
    public function it_fails_on_update_customer_with_empty_request_body()
    {
        $customer = Customer::factory()->create();

        $response = $this->put('/api/v1/customers/' . $customer->id, []);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Empty update request received, At least one field must be provided for update'
        ]);
    }

    #[Test]
    public function it_deletes_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->delete('/api/v1/customers/' . $customer->id);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function it_fails_on_delete_customer_with_non_existing_id()
    {
        $response = $this->delete('/api/v1/customers/99999');
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Customer not found']);
    }
}
