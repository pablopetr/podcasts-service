<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresPremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $claims = $request->attributes->get('jwt_claims');

        if (! $claims) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $membership = is_array($claims) ?
            ($claims['membership'] ?? null)
            : ($claims->membership ?? null);

        if ($membership !== 'premium') {
            return response()->json(['message' => 'Premium membership required'], 403);
        }

        return $next($request);
    }
}
