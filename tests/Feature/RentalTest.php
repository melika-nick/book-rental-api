<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class RentalTest extends TestCase
{
    use RefreshDatabase;

    protected function memberToken()
    {
        $member = User::factory()->create(['role'=>'member']);
        return [$member, $member->createToken('member-token')->plainTextToken];
    }

    public function test_member_can_rent_book()
    {
        [$member, $token] = $this->memberToken();
        $book = Book::factory()->create(['stock'=>3]);

        $response = $this->postJson("/api/member/rentals", [
            'book_id' => $book->id
        ], ['Authorization'=>"Bearer $token"]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rentals', ['book_id'=>$book->id,'user_id'=>$member->id]);
    }

    public function test_member_can_return_book_and_calculate_fine()
    {
        [$member, $token] = $this->memberToken();
        $book = Book::factory()->create(['stock'=>3]);
        $rental = Rental::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(10),
            'due_at' => now()->subDays(7),
        ]);

        $response = $this->postJson("/api/member/rentals/{$rental->id}/return", [], ['Authorization'=>"Bearer $token"]);
        $response->assertStatus(200);

        $rental->refresh();
        $this->assertNotNull($rental->returned_at);
        $this->assertGreaterThan(0, $rental->fine_amount);
    }
}
