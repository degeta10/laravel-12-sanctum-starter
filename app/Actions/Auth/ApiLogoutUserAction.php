<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Http\Request;

final class ApiLogoutUserAction
{
    /**
     * Logout the user by deleting their current access token.
     */
    public function execute(Request $request): bool
    {
        return (bool) $request->user()?->currentAccessToken()?->delete();
    }
}
