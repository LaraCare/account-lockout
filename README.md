# LaraCare Account Lockout

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lara-care/account-lockout.svg?style=flat-square)](https://packagist.org/packages/lara-care/account-lockout)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lara-care/account-lockout/run-tests.yml?branch=main&label=tests)](https://github.com/lara-care/account-lockout/actions)
[![License](https://img.shields.io/packagist/l/lara-care/account-lockout.svg?style=flat-square)](LICENSE.md)

A robust account lockout and progressive cooldown penalty manager for Laravel applications. Protect your authentication routes against brute-force attacks with configurable attempt thresholds, progressive lockouts, and event dispatching.

---

## Features

- 🔒 **Threshold-based Locking:** Temporarily lock accounts or IP addresses after a specified number of failed login attempts.
- ⏱️ **Progressive Cooldowns:** Apply escalating lockout durations for repeat offenders (e.g., 15 mins → 60 mins → 24 hours).
- 📢 **Event Driven:** Dispatches `AccountLocked` and `AccountUnlocked` events for audit logs or notifications.
- 🔔 **Built-in Notifications:** Automatically trigger security alert emails/notifications when lockouts occur.
- 🛠️ **Simple API:** Intuitive interface for recording failed attempts, checking status, and manually unlocking accounts.

---

## Installation

You can install the package via Composer:

```bash
composer require lara-care/account-lockout
Publish the configuration file:Bashphp artisan vendor:publish --tag="account-lockout-config"
This will create a config/account-lockout.php file in your application root:PHPreturn [
    /*
    |--------------------------------------------------------------------------
    | Max Login Attempts
    |--------------------------------------------------------------------------
    |
    | Maximum number of failed attempts allowed before triggering a lockout.
    |
    */
    'max_attempts' => 5,

    /*
    |--------------------------------------------------------------------------
    | Progressive Decay Minutes
    |--------------------------------------------------------------------------
    |
    | Cooldown periods (in minutes) for consecutive lockouts.
    |
    */
    'cooldown_penalties' => [
        1 => 15,  // First lockout: 15 minutes
        2 => 60,  // Second lockout: 60 minutes
        3 => 1440 // Third lockout: 24 hours
    ],
];
UsageRecording Failed Attempts & Checking LockoutsUse the LockoutManager service inside your login controllers or authentication actions:PHPuse LaraCare\AccountLockout\Services\LockoutManager;

class LoginController extends Controller
{
    public function login(Request $request, LockoutManager $lockout)
    {
        $user = User::where('email', $request->email)->first();

        // Check if the user is currently locked out
        if ($user && $lockout->isLocked($user)) {
            return response()->json([
                'message' => 'Your account is locked due to multiple failed login attempts.'
            ], 423);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            if ($user) {
                // Record the failed attempt
                $lockout->recordFailedAttempt($user, $request->ip());
            }

            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // Reset lockout count on successful login
        $lockout->unlock($user);

        return response()->json(['message' => 'Login successful.']);
    }
}
EventsThe package fires the following events:EventDescriptionLaraCare\AccountLockout\Events\AccountLockedDispatched when an account reaches the maximum failed attempts threshold. Contains $event->user and $event->unlocksAt.LaraCare\AccountLockout\Events\AccountUnlockedDispatched when an account is manually unlocked or cleared. Contains $event->user.TestingRun the test suite using PHPUnit:Bashvendor/bin/phpunit
LicenseThe MIT License (MIT). Please see License File for more information.
---

### Create the file directly in terminal

Run this command from your project root directory (`/home/popstudio/Documents/Personal/Projects/Lara-care/account-lockout`):

```bash
cat << 'EOF' > README.md
# LaraCare Account Lockout

A robust account lockout and progressive cooldown penalty manager for Laravel applications. Protect your authentication routes against brute-force attacks with configurable attempt thresholds, progressive lockouts, and event dispatching.

## Features
- 🔒 **Threshold-based Locking:** Lock accounts after max failed attempts.
- ⏱️ **Progressive Cooldowns:** Apply escalating lockout durations for repeat offenders.
- 📢 **Event Driven:** Dispatches `AccountLocked` and `AccountUnlocked` events.
- 🛠️ **Simple API:** Simple methods to record attempts, check status, and unlock.

## Installation
```bash
composer require lara-care/account-lockout
Publish configuration:Bashphp artisan vendor:publish --tag="account-lockout-config"
TestingBashvendor/bin/phpunit
LicenseThe MIT License (MIT).EOF
---

### Commit and Push

Once created, stage, commit, and push your new `README.md`:

```bash
git add README.md
git commit -m "docs: add initial README.md documentation"
git push origin main