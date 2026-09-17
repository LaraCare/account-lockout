<?php

namespace LaraCare\AccountLockout\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AccountLockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected int $lockoutDurationMinutes) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $expiration = config('account-lockout.signed_url_expiration', 30);

        $unlockUrl = URL::temporarySignedRoute(
            'lara-care.unlock',
            now()->addMinutes($expiration),
            ['id' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject('Security Alert: Your Account Has Been Locked')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your account was temporarily locked due to multiple failed login attempts.")
            ->line("It will automatically unlock in {$this->lockoutDurationMinutes} minutes.")
            ->action('Unlock Account Now', $unlockUrl)
            ->line('If you did not attempt to log in, we strongly recommend resetting your password immediately.');
    }
}