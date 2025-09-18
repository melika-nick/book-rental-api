<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'member')->get();
        $books = Book::all();

        foreach ($books as $book) {
            if (rand(0, 1)) {
                Rental::factory()->create([
                    'user_id' => $users->random()->id,
                    'book_id' => $book->id,
                ]);
            }
        }
    }
}
