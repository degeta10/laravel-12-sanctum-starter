<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class WebLoginUserAction
{
    /**
     * Authenticate the user using the provided credentials and start a session.
     */
    public function execute(array $credentials, Request $request): ?User
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        $request->session()->regenerate();

        return $request->user();
    }
}
