<?php

namespace LaraCare\AccountLockout\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use DateTimeInterface;

class AccountLocked
{
    use Dispatchable, SerializesModels;

    public mixed $user;
    public ?DateTimeInterface $unlocksAt;

    public function __construct(mixed $user, ?DateTimeInterface $unlocksAt = null)
    {
        $this->user = $user;
        $this->unlocksAt = $unlocksAt;
    }
}