<?php

namespace LaraCare\AccountLockout\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountLocked
{
    use Dispatchable, SerializesModels;

    public mixed $user;

    public function __construct(mixed $user)
    {
        $this->user = $user;
    }
}