<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebLoginUserAction
{
    public function execute(array $credentials, Request $request): ?User
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        $request->session()->regenerate();

        return $request->user();
    }
}
