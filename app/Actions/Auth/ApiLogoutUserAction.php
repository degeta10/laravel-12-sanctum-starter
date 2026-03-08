<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;

class ApiLogoutUserAction
{
    public function execute(Request $request): void
    {
        $request->user()?->currentAccessToken()?->delete();
    }
}
