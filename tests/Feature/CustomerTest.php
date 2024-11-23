<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }

    /**
     * Test retrieving all customers with default pagination.
     */
    public function testGetAllCustomersDefaultPagination()
    {
        Customer::factory()->count(15)->create();

        $response = $this->get('/api/v1/customers');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'surname', 'balance', 'created_at', 'updated_at']
            ]
        ]);
    }
}
