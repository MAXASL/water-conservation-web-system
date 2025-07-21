<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware {
    protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        return route('login');
    }
}


}
