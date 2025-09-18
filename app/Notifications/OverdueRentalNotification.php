<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueRentalNotification extends Notification
{
    use Queueable;

    protected $rentals;
    /**
     * Create a new notification instance.
     */
    public function getRentals()
    {
        return $this->rentals;
    }
    public function __construct($rentals)
    {
        $this->rentals = $rentals;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dailyFine = (int) env('DAILY_FINE', 20000);
        $mail = (new MailMessage)
            ->subject('یادآوری — کتاب(های) معوق')
            ->greeting("سلام {$notifiable->name},")
            ->line('شما کتاب(های) زیر را دیر برگردانده‌اید:');

        foreach ($this->rentals as $r) {
            $due = $r->due_at ? Carbon::parse($r->due_at)->toDateString() : '-';
            $daysLate = Carbon::now()->diffInDays($r->due_at);
            $fine = $daysLate * $dailyFine;
            $mail->line("• {$r->book->title} — زمان مقرر: {$due} — تأخیر: {$daysLate} روز — جریمه فعلی: {$fine} ریال");
        }

        $mail->line('لطفاً هر چه سریع‌تر کتاب(ها) را بازگردانید یا با پشتیبانی تماس بگیرید.')
            ->action('مشاهده رنتال‌ها', url('/')) //frontend
            ->salutation('با تشکر، تیم پشتیبانی');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
