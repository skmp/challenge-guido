<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Client;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'client_id' => Client::factory(),
            'booked_on' => now(),
        ];
    }
}
