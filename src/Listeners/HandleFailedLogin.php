<?php
namespace LaraCare\AccountLockout\Listeners;

use Illuminate\Auth\Events\Failed;
use LaraCare\AccountLockout\Services\LockoutManager;

class HandleFailedLogin
{
    public function __construct(protected LockoutManager $manager) {}

    public function handle(Failed $event): void
    {
        if ($event->user) {
            $this->manager->recordFailedAttempt(
                $event->user,
                request()->ip()
            );
        }
    }
}