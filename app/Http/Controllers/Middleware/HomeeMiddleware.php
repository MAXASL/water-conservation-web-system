<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeeMiddleware {
    public function handle(Request $request, Closure $next) {
        if (Auth::user() && Auth::user()->user_type === 'homee') {
            return $next($request);
        }
        return redirect('/homee')->withErrors(['error' => 'Unauthorized access']);
    }
}
