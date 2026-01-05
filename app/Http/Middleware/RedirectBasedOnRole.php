<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            
            // If admin accessing /dashboard, redirect to /admin
            if ($user->is_admin && $request->is('dashboard*')) {
                return redirect('/admin');
            }
            
            // If non-admin accessing /admin, redirect to /dashboard
            if (!$user->is_admin && $request->is('admin*')) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
