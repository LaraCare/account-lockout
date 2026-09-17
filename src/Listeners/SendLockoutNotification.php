<?php

namespace LaraCare\AccountLockout\Listeners;

use LaraCare\AccountLockout\Events\AccountLocked;
use LaraCare\AccountLockout\Notifications\AccountLockedNotification;

class SendLockoutNotification {
    public function handle(AccountLocked $event): void {
        if (config('account-lockout.unlock_via_email', true)) {
            $event->user->notify(new AccountLockedNotification(
            $event->unlocksAt->diffInMinutes(now())
            ));
        }
    }
}