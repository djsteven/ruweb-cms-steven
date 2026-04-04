<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $this->resolveBearerToken($request);

        if (! $apiKey) {
            return $this->unauthorizedResponse('Missing MCP API key.');
        }

        $user = User::findByMcpApiKey($apiKey);

        if (! $user) {
            return $this->unauthorizedResponse('Invalid MCP API key.');
        }

        $user->markMcpApiKeyAsUsed();
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    protected function resolveBearerToken(Request $request): ?string
    {
        $token = $request->bearerToken();

        return is_string($token) && $token !== '' ? $token : null;
    }

    protected function unauthorizedResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}
