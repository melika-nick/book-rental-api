<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function adminToken()
    {
        $admin = User::factory()->create(['role'=>'admin']);
        return $admin->createToken('admin-token')->plainTextToken;
    }

    public function test_admin_can_create_book()
    {
        $token = $this->adminToken();
        $response = $this->postJson('/api/admin/books', [
            'title' => 'Book Title',
            'author' => 'Author Name',
            'isbn' => '1234567890',
            'stock' => 5,
            'published_at' => '2025-01-01'
        ], ['Authorization'=>"Bearer $token"]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title'=>'Book Title']);
    }

    public function test_member_cannot_create_book()
    {
        $member = User::factory()->create(['role'=>'member']);
        $token = $member->createToken('member-token')->plainTextToken;

        $response = $this->postJson('/api/admin/books', [
            'title' => 'Book Title',
            'author' => 'Author Name',
            'isbn' => '1234567890',
            'stock' => 5,
            'published_at' => '2025-01-01'
        ], ['Authorization'=>"Bearer $token"]);

        $response->assertStatus(403); // ممنوع
    }
}
