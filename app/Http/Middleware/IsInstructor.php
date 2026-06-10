<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsInstructor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->role !== 'instructor') {
            return redirect()->route('login');
        }

        if (! is_null(Auth::user()->is_active) && ! (bool) Auth::user()->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        if (Auth::user()->approve_status !== 'approved') {
            return redirect('/instructor/pending');
        }

        return $next($request);
    }
}
