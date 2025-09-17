<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
        }

        $books = $query->paginate(10);

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'هیچ کتابی یافت نشد.'
            ], 404);
        }

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json([
                'message' => 'کتاب مورد نظر یافت نشد.'
            ], 404);
        }

        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, Book $book) {
        $book->update($request->validated());
        return new BookResource($book);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json([
                'message' => 'کتاب مورد نظر برای حذف یافت نشد.'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'کتاب با موفقیت حذف شد.'
        ], 200);
    }
}
