<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roleType): Response
    {
        // 0=Patient, 1=Doctor, 2=Admin
        
        $roleMap = [
            'patient' => 0,
            'doctor' => 1,
            'admin' => 2,
        ];

        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role;

        // If the user's role matches the required role, let them pass
        if ($userRole == $roleMap[$roleType]) {
            return $next($request);
        }

        // If not, abort with 403 Forbidden
        abort(403, 'Unauthorized Access');
    }
}