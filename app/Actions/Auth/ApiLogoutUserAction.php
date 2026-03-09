<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Http\Request;

final class ApiLogoutUserAction
{
    public function execute(Request $request): void
    {
        $request->user()?->currentAccessToken()?->delete();
    }
}
