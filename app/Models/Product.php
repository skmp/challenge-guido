<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function scopeWithBookingsCount($query)
    {
        $query->leftJoin('bookings', function ($join) {
                $join->on('products.id', '=', 'bookings.product_id');
            })
            ->select('products.*', DB::raw('count(bookings.id) as booked'))
            ->groupBy('products.id');
    }
}
