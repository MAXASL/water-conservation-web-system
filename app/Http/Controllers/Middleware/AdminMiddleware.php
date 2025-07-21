<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    // app/Http/Middleware/AdminMiddleware.php
public function handle(Request $request, Closure $next)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login');
    }

    if (!Auth::user()->is_admin) {
        return redirect('/')->with('error', 'Admin access only'); // Changed from logout
    }

    return $next($request);
}

Log::debug('Admin Middleware Check', [
        'is_authenticated' => Auth::check(),
        'user_id' => Auth::check() ? Auth::id() : null,
        'is_admin' => Auth::check() ? Auth::user()->is_admin : null,
        'session_id' => session()->getId()
    ]);
}
