<?php

namespace App\Http\Middleware;

use App\Services\Auth\UserSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    public function __construct(
        protected UserSessionService $sessionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $this->sessionService->createForRequest($request);
        }

        return $response;
    }
}
