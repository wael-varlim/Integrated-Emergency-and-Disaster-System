<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user  = $request->user();
        $token = $user?->currentAccessToken();

        if ($token && $token->created_at->diffInDays(now()) > 15) {
            $token->delete();
            $newToken = $user->createToken($token->name)->plainTextToken;
            $response->headers->set('X-New-Token', $newToken);
        }

        return $response;
    }
}