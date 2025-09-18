<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Notifications\OverdueRentalNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueRentals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:check-overdue {--repeat : allow repeated notifications for same rental}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find overdue rentals and notify users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // پایه: رکوردهایی که هنوز برگشته‌اند و due_at گذشته و هنوز notified نشده
        $query = Rental::with(['user', 'book'])
            ->whereNull('returned_at')
            ->where('due_at', '<', $now);

        // اگر گزینه repeat زده نشده، فقط رکوردهایی که notified نیستن بگیر
        if (! $this->option('repeat')) {
            $query->whereNull('overdue_notified_at');
        }
        $overdue = $query->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue rentals found.');
            return 0;
        }

        // گروه‌بندی بر اساس user_id تا برای هر کاربر یک ایمیل حاوی همه رنتال‌ها ارسال کنیم
        $byUser = $overdue->groupBy('user_id');

        foreach ($byUser as $userId => $rentals) {
            $user = $rentals->first()->user;
            if (! $user || ! $user->email) {
                $this->warn("User #{$userId} not found or has no email — skipping.");
                continue;
            }

            // ارسال Notification (قابل queue)
            $user->notify(new OverdueRentalNotification($rentals));

            // به‌روزرسانی overdue_notified_at برای رکوردهای ارسالی
            $ids = $rentals->pluck('id')->toArray();
            Rental::whereIn('id', $ids)->update(['overdue_notified_at' => Carbon::now()]);

            $this->info("Notified user {$user->email} about " . count($rentals) . " rentals.");
        }

        return 0;
    }
}
