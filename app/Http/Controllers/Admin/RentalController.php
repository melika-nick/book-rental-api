<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Book;
use App\Models\Rental;
use Carbon\Carbon;

class RentalController extends Controller
{
    private function isAvailable(Book $book)
    {
        if($book->stock <= 0){
            return response()->json(['message' => 'this book is not available']);
        }
        return true;
    }

    private function create(Book $book)
    {
        $rental = Rental::create([
            'user_id'    => auth()->id(),
            'book_id'    => $book->id,
            'rented_at'  => Carbon::now(),
            'due_at'     => Carbon::now()->addDays(7),
            'fine_amount'=> 0,
        ]);
        return new RentalResource($rental);
    }

    private function fine(Rental $rental): int
    {
        if ($rental->returned_at && $rental->returned_at->gt($rental->due_at)) {
            $daysLate = $rental->returned_at->diffInDays($rental->due_at);
            return $daysLate * env('DAILY_FINE', 20000);
        }

        return 0;
    }
    public function index()
    {
        $rentals = Rental::with(['book', 'user'])->paginate(10);
        return new RentalResource($rentals);
    }

    public function store(RentalRequest $request)
    {
        $book = Book::findorfail($request->book_id);
        $this->isAvailable($book);
        $rental = $this->create($book);
        $book->decrement('stock');
        return new RentalResource($rental);
    }

    public function returnBook($id)
    {
        $rental = Rental::findOrFail($id);

        if ($rental->returned_at) {
            return response()->json(['message' => 'this book had been returned before'], 400);
        }

        $rental->returned_at = now();
        $rental->fine_amount = $this->fine($rental);
        $rental->save();

        $rental->book->increment('stock');

        return response()->json([
            'message' => 'book returned successfully',
            'fine'    => $rental->fine_amount,
        ]);
    }
    public function show(Rental $rental)
    {
        return new RentalResource($rental->load(['book', 'user']));
    }
}
