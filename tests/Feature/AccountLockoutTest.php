<?php

namespace LaraCare\AccountLockout\Tests\Feature;

// use Illuminate\Foundation\Auth\User;
use LaraCare\AccountLockout\Tests\Stubs\User;
use Illuminate\Support\Facades\Event;
use LaraCare\AccountLockout\Events\AccountLocked;
use LaraCare\AccountLockout\Services\LockoutManager;
use LaraCare\AccountLockout\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class AccountLockoutTest extends TestCase {
    protected LockoutManager $manager;
    protected User $user;

    protected function setUp(): void {
        parent::setUp();

        $this->manager = app(LockoutManager::class);

        // Fix mass assignment exception by using forceFill
        $this->user = (new User)->forceFill([
            'id' => 1,
            'email' => 'developer@lara-care.dev',
        ]);
    }

    /** @test */
    public function it_locks_account_after_reaching_max_attempts() {
        Event::fake([AccountLocked::class]);

        // Simulate 5 failed attempts (default threshold)
        for ($i = 1; $i <= 5; $i++) {
            $this->manager->recordFailedAttempt($this->user, '127.0.0.1');
        }

        $this->assertTrue($this->manager->isLocked($this->user));
        Event::assertDispatched(AccountLocked::class);
    }


    /** @test */
    public function it_applies_progressive_cooldown_penalties() {
        Notification::fake(); // Prevents real notifications from firing

        // First lockout -> 15 mins
        for ($i = 1; $i <= 5; $i++) {
            $this->manager->recordFailedAttempt($this->user, '127.0.0.1');
        }
        $this->assertTrue($this->manager->isLocked($this->user));

        // Unlock manually to test repeat offender penalty
        $this->manager->unlock($this->user);
        $this->assertFalse($this->manager->isLocked($this->user));

        // Second lockout -> 60 mins (as per progressive config)
        for ($i = 1; $i <= 5; $i++) {
            $this->manager->recordFailedAttempt($this->user, '127.0.0.1');
        }

        $this->assertTrue($this->manager->isLocked($this->user));
    }

}