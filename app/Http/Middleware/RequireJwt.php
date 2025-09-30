<?php

namespace App\Http\Middleware;

use App\Services\Auth\JwtVerifier;
use Closure;
use Illuminate\Http\Request;

class RequireJwt
{
    public function __construct(private JwtVerifier $verifier) {}

    public function handle(Request $request, Closure $next, ...$audiences)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['message'=>'Missing bearer token'], 401);

        try {
            $required = $audiences ?: null;
            $res = $this->verifier->validate($token, $required);
            $request->attributes->set('jwt_claims', $res['claims']);
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Invalid token'], 401);
        }

        return $next($request);
    }
}
