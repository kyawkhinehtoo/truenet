<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
             if (Auth::check() && Auth::user()->disabled==1) {
                Auth::logout();

                $request->session()->invalidate();
    
                $request->session()->regenerateToken();
    
                return redirect()->route('login')->with('error', 'Your Account is suspended, please contact Admin.');
            }

    return $next($request);
    }
}
