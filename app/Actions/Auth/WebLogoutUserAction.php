<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WebLogoutUserAction
{
    /**
     * Logout the user by invalidating their session.
     */
    public function execute(Request $request): bool
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return true;
    }
}
