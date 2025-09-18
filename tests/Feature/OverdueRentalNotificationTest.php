<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rental;
use App\Models\Book;
use App\Notifications\OverdueRentalNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OverdueRentalNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_to_users_with_overdue_rentals()
    {
        Notification::fake();

        // ساخت کاربر
        $user = User::factory()->create();

        // ساخت کتاب
        $book = Book::factory()->create([
            'title' => 'Laravel Deep Dive',
        ]);

        // ساخت رنتال عقب‌افتاده
        $rental = Rental::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_at' => Carbon::now()->subDays(3), // سه روز گذشته
        ]);

        // اجرای کامند
        $this->artisan('rentals:check-overdue')
            ->assertExitCode(0);

        // انتظار ارسال نوتیفیکیشن
        Notification::assertSentTo(
            [$user],
            OverdueRentalNotification::class,
            function ($notification, $channels) use ($rental) {
                return $notification->getRentals()->first()->id === $rental->id;
            }
        );
    }

    /** @test */
    public function it_does_not_send_email_if_no_overdue_rentals()
    {
        Notification::fake();

        $user = User::factory()->create();

        // رنتال سالم (موعد آینده)
        Rental::factory()->create([
            'user_id' => $user->id,
            'due_at' => Carbon::now()->addDays(2),
        ]);

        $this->artisan('rentals:check-overdue')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
