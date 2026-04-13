<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product1;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        
        $this->product1 = Product::create([
            'name' => 'Product 1',
            'price' => 10.00,
            'stock' => 50,
        ]);
        
        $this->product2 = Product::create([
            'name' => 'Product 2',
            'price' => 25.50,
            'stock' => 10,
        ]);
    }

    public function test_user_can_create_order(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'products' => [
                ['id' => $this->product1->id, 'quantity' => 2],
                ['id' => $this->product2->id, 'quantity' => 1],
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'uuid',
                'user_id',
                'total_price',
                'status',
            ]
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_price' => 45.50,
            'status' => OrderStatus::Pending->value,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock' => 48,
        ]);
    }

    public function test_user_cannot_order_with_insufficient_stock(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'products' => [
                ['id' => $this->product2->id, 'quantity' => 15],
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('products');
    }

    public function test_user_can_pay_order(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'products' => [
                ['id' => $this->product1->id, 'quantity' => 1],
            ]
        ]);

        $orderId = $response->json('data.id');

        $payResponse = $this->actingAs($this->user, 'sanctum')->postJson("/api/orders/{$orderId}/pay");

        $payResponse->assertStatus(200);
        
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => OrderStatus::Paid->value,
        ]);
    }
    
    public function test_user_can_list_orders(): void
    {
        $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'products' => [
                ['id' => $this->product1->id, 'quantity' => 1],
            ]
        ]);
        
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/orders');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
    
    public function test_user_can_show_order(): void
    {
        $createResponse = $this->actingAs($this->user, 'sanctum')->postJson('/api/orders', [
            'products' => [
                ['id' => $this->product1->id, 'quantity' => 1],
            ]
        ]);
        
        $orderId = $createResponse->json('data.id');
        
        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/orders/{$orderId}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $orderId);
    }
}
