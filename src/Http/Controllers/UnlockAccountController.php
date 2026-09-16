<?php

namespace LaraCare\AccountLockout\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaraCare\AccountLockout\Services\LockoutManager;

class UnlockAccountController extends Controller
{
    public function __invoke(Request $request, LockoutManager $manager)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid or expired unlock link.');
        }

        $userModel = config('auth.providers.users.model');
        $user = $userModel::findOrFail($request->route('id'));

        $manager->unlock($user);

        return redirect()->route('login')
            ->with('status', 'Your account has been successfully unlocked. You may now log in.');
    }
}