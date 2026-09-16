<?php

namespace LaraCare\AccountLockout\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use LaraCare\AccountLockout\Events\AccountLocked;
use LaraCare\AccountLockout\Events\AccountUnlocked;

class LockoutManager
{
    public function __construct(protected array $config) {}

    public function recordFailedAttempt(Authenticatable $user, ?string $ip = null): void
    {
        $record = DB::table('lara_care_lockouts')
            ->where('authenticatable_type', get_class($user))
            ->where('authenticatable_id', $user->getAuthIdentifier())
            ->whereNull('cleared_at')
            ->first();

        $attempts = ($record->failed_attempts ?? 0) + 1;
        $maxAttempts = $this->config['max_attempts'] ?? 5;

        if ($record) {
            DB::table('lara_care_lockouts')->where('id', $record->id)->update([
                'failed_attempts' => $attempts,
                'ip_address' => $ip ?? $record->ip_address,
                'updated_at' => now(),
            ]);
        } else {
            $recordId = DB::table('lara_care_lockouts')->insertGetId([
                'authenticatable_type' => get_class($user),
                'authenticatable_id' => $user->getAuthIdentifier(),
                'ip_address' => $ip,
                'failed_attempts' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $record = (object) ['id' => $recordId];
        }

        if ($attempts >= $maxAttempts) {
            $this->lock($user, $record->id);
        }
    }

    public function lock(Authenticatable $user, ?int $recordId = null): void
    {
        $duration = $this->calculateDuration($user);
        $unlocksAt = now()->addMinutes($duration);

        DB::table('lara_care_lockouts')
            ->where('authenticatable_type', get_class($user))
            ->where('authenticatable_id', $user->getAuthIdentifier())
            ->whereNull('cleared_at')
            ->update([
                'locked_at' => now(),
                'unlocks_at' => $unlocksAt,
                'updated_at' => now(),
            ]);

        event(new AccountLocked($user, $unlocksAt));
    }

    public function isLocked(Authenticatable $user): bool
    {
        $record = $this->getLockRecord($user);

        if (! $record || ! $record->unlocks_at) {
            return false;
        }

        if (now()->greaterThanOrEqualTo($record->unlocks_at)) {
            $this->unlock($user);
            return false;
        }

        return true;
    }

    public function unlock(Authenticatable $user): void
    {
        DB::table('lara_care_lockouts')
            ->where('authenticatable_type', get_class($user))
            ->where('authenticatable_id', $user->getAuthIdentifier())
            ->whereNull('cleared_at')
            ->update([
                'cleared_at' => now(),
                'updated_at' => now(),
            ]);

        event(new AccountUnlocked($user));
    }

    protected function calculateDuration(Authenticatable $user): int
    {
        if (! ($this->config['progressive']['enabled'] ?? false)) {
            return $this->config['lockout_duration'] ?? 15;
        }

        $priorLockouts = DB::table('lara_care_lockouts')
            ->where('authenticatable_type', get_class($user))
            ->where('authenticatable_id', $user->getAuthIdentifier())
            ->whereNotNull('locked_at')
            ->count();

        $penalties = $this->config['progressive']['penalties'] ?? [];
        
        return $penalties[$priorLockouts] ?? end($penalties) ?: 15;
    }

    protected function getLockRecord(Authenticatable $user)
    {
        return DB::table('lara_care_lockouts')
            ->where('authenticatable_type', get_class($user))
            ->where('authenticatable_id', $user->getAuthIdentifier())
            ->whereNull('cleared_at')
            ->first();
    }
}