<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Product;
use App\Models\Client;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $bookings = Booking::factory(3)->create();

        $response = $this->get('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testSuccessfulBooking()
    {
        $product = Product::factory()->create(['capacity' => 1]);
        $client = Client::factory()->create();

        $response = $this->post('/api/bookings', [
            'product_id' => $product->id,
            'client_id' => $client->id,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Booking created']);

        $this->assertDatabaseHas('bookings', [
            'product_id' => $product->id,
            'client_id' => $client->id,
        ]);
    }

    public function testFullyBookedEvent()
    {
        Booking::truncate();

        $this->assertDatabaseCount('bookings', 0);
        $product = Product::factory()->create(['capacity' => 1]);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        $this->assertDatabaseCount('bookings', 0);
        
        dump(Product::all());

        Booking::create([
            'product_id' => $product->id,
            'client_id' => $client1->id,
            'booked_on' => now(),
        ]);

        dump(Booking::all());

        $this->assertDatabaseCount('bookings', 1);

        $response = $this->post('/api/bookings', [
            'product_id' => $product->id,
            'client_id' => $client2->id,
        ]);

        dump(Booking::all());

        $this->assertDatabaseCount('bookings', 1);

        $response->assertStatus(409)
            ->assertJson(['message' => 'Product is already fully booked']);

    }

    public function testBookingAlreadyExists()
    {
        $product = Product::factory()->create(['capacity' => 1]);
        $client = Client::factory()->create();

        Booking::create([
            'product_id' => $product->id,
            'client_id' => $client->id,
            'booked_on' => now(),
        ]);

        $this->assertDatabaseCount('bookings', 1);

        $response = $this->post('/api/bookings', [
            'product_id' => $product->id,
            'client_id' => $client->id,
        ]);

        $response->assertStatus(409)
            ->assertJson(['message' => 'Booking already exists']);

        $this->assertDatabaseCount('bookings', 1);
    }
}
