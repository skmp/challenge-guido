<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::withBookingsCount()->get();

        $data = $products->map(function ($product) {
            $available = $product->booked < $product->capacity;
            $product->setAttribute('available', $available);
            return $product->only(['id', 'title', 'description', 'available']);
        });

        return response()->json($data);
    }
}
