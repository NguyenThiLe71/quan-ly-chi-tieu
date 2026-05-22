<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
{
    if (!Auth::check() || Auth::user()->role !== $role) {
        abort(403, 'Bạn không có quyền truy cập.');
    }

    return $next($request);
}
}