<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Book;
use App\Models\Rental;

class RentalController extends Controller
{
    private function isAvailable(Book $book)
    {
        if($book->stock <= 0){
            abort(400, 'This book is not available');
        }
        return true;
    }

    private function fine(Rental $rental): int
    {
        if ($rental->returned_at && $rental->returned_at->gt($rental->due_at)) {
            $daysLate = $rental->due_at->diffInDays($rental->returned_at);
            return $daysLate * env('DAILY_FINE', 20000);
        }

        return 0;
    }
    public function index()
    {
        $rentals = Rental::with(['book', 'user'])->paginate(10);
        return RentalResource::collection($rentals);
    }
    public function store(RentalRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        // بررسی موجودی
        $this->isAvailable($book);

        // کاهش موجودی قبل از اجاره
        $book->decrement('stock');

        // ایجاد اجاره
        $rental = Rental::create([
            'user_id'    => auth()->id() ?? 1,
            'book_id'    => $book->id,
            'rented_at'  => now(),
            'due_at'     => now()->addDays(7),
            'fine_amount'=> 0,
        ]);

        return new RentalResource($rental->load(['book', 'user']));
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
