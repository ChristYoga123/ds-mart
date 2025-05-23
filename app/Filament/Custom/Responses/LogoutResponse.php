<?php

namespace App\Filament\Custom\Responses;

use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as ContractsLogoutResponse;

class LogoutResponse implements ContractsLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        // change this to your desired route
        return redirect()->route('login');
    }
}