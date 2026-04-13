<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    public function test_can_list_products(): void
    {
        Product::create([
            'name' => 'Test Product',
            'description' => 'A test description',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_can_create_product(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/products', [
            'name' => 'New Product',
            'description' => 'Brand new product',
            'price' => 150.00,
            'stock' => 20,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }
    
    public function test_can_update_product(): void
    {
        $product = Product::create([
            'name' => 'Old Product',
            'price' => 10.00,
            'stock' => 5,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 15.00,
            'stock' => 10,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product', 'price' => 15.00, 'stock' => 10]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::create([
            'name' => 'To Delete',
            'price' => 10.00,
            'stock' => 5,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_regular_user_cannot_create_product(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        
        $response = $this->actingAs($regularUser, 'sanctum')->postJson('/api/products', [
            'name' => 'Should Fail',
            'price' => 100,
            'stock' => 10,
        ]);

        $response->assertStatus(403);
    }
}
