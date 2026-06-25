<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetContentLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language');

        if ($lang === null) {
            return $this->apiResponse(null, 'Accept-Language header is required', 400);
        }

        $allowed = ['en', 'ar'];

        if (! in_array($lang, $allowed, true)) {
            return $this->apiResponse(null, 'Unsupported language', 422);
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
