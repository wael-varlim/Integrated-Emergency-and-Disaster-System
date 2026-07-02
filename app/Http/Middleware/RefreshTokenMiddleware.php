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
        $oldToken = $user?->currentAccessToken();

        //if ($token && $token->created_at->diffInDays(now()) > 15) {
        if ($oldToken && $oldToken->created_at->diffInMinutes(now()) > 2) 
        {    
            $newToken = $user->createToken($oldToken->name)->plainTextToken;
            $response->headers->set('X-New-Token', $newToken);

            $body = json_decode($response->getContent(), true);
            $body['data']['new_access_token'] = $newToken;
            $response->setContent(json_encode($body));

            $oldToken->delete();
        }

        return $response;
    }
}