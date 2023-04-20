<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $product_id = $request->input('product_id');
        $client_id = $request->input('client_id');
        
        $bookingsCount = Booking::where('product_id', $product_id)
        ->count();

    $productCapacity = Product::where('id', $product_id)
        ->value('capacity');

    if ($bookingsCount >= $productCapacity) {
        return response()->json(['message' => 'Product is already fully booked'], 409);
    }

    $booking = new Booking([
        'product_id' => $product_id,
        'client_id' => $client_id,
        'booked_on' => now(),
    ]);

    try {
        $booking->save();
        return response()->json(['message' => 'Booking created'], 201);
    } catch (\Exception $e) {
        $errorCode = $e->errorInfo[1];
        if ($errorCode == 1062) {
            return response()->json(['message' => 'Booking already exists'], 409);
        }
        return response()->json(['message' => 'Error creating booking'], 409);
    }

        

        $booking = new Booking();
        $booking->product_id = $product_id;
        $booking->client_id = $client_id;
        $booking->booked_on = now();
        
        try {
            $insertedRows = DB::table('bookings')
            ->whereNotExists(function ($query) use ($product_id) {
                $query->select(DB::raw(1))
                    ->from('bookings')
                    ->whereColumn('product_id', '=', $product_id)
                    ->groupBy('product_id')
                    ->havingRaw('COUNT(bookings.id) >= products.capacity');
            })
            ->whereExists(function ($query) use ($product_id) {
                $query->select(DB::raw(1))
                    ->from('products')
                    ->where('id', '=', $product_id);
            })
            ->insert($booking->toArray());

            if ($insertedRows == 0) {
                return response()->json(['message' => 'Product is already fully booked'], 409);
            }
            
            return response()->json(['message' => 'Booking created'], 201);
        } catch (\Illuminate\Database\QueryException $exception) {
            // this is a bit tricky, but eeh, also maybe not atomic?
            // Anyway bookings can't be deleted from the db right now so

            // Check if booking already exists
            $existingBookingCount = Booking::where('product_id', $product_id)
            ->where('client_id', $client_id)
            ->count();
        
            if ($existingBookingCount > 0) {
                return response()->json(['message' => 'Booking already exists'], 409);
            }

            // handle any other database errors here
            return response()->json(['message' => 'Error creating booking'], 409);
        }
    }
}
