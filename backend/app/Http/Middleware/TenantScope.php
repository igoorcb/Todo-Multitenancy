<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->tenant_id) {
            return response()->json([
                'message' => 'User does not belong to any tenant'
            ], 403);
        }

        return $next($request);
    }
}
