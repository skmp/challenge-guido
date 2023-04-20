<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Client;
use App\Models\Booking;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndex()
    {
        // Create two products with different capacities
        $products = Product::factory()->count(2)->create([
            'capacity' => [1, 2],
        ]);

        // Create a client
        $client = Client::factory()->create();

        // Make a booking for the first product with the client
        $products[0]->bookings()->create([
            'client_id' => $client->id,
            'booked_on' => now(),
        ]);

        $response = $this->get('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'available',
                    ],
                ],
            ]);

        $responseData = $response->json('data');

        // Check that the first product is fully booked and the second product is available
        $this->assertEquals($responseData[0]['id'], $products[0]->id);
        $this->assertEquals($responseData[0]['title'], $products[0]->title);
        $this->assertEquals($responseData[0]['description'], $products[0]->description);
        $this->assertEquals($responseData[0]['available'], False);

        $this->assertEquals($responseData[1]['id'], $products[1]->id);
        $this->assertEquals($responseData[1]['title'], $products[1]->title);
        $this->assertEquals($responseData[1]['description'], $products[1]->description);
        $this->assertEquals($responseData[1]['available'], True);
    }
}