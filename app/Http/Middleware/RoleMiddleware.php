<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // role column: 1 = admin, 0 = customer
        $roles = [
            'admin'    => 1,
            'customer' => 0,
        ];
    
        if (!auth()->check() || (int) auth()->user()->role !== ($roles[$role] ?? null)) {
            abort(403, 'You are not allowed to open this page.');
        }
    
        return $next($request);
    }
}