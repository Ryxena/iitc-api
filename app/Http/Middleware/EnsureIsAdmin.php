<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403, 'Unauthorized. Admin only.');
        }

        return $next($request);
    }
}
