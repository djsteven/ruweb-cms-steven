<?php

namespace App\Http\Middleware;

use App\Models\OauthAccessToken;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->resolveBearerToken($request);

        if (! $token) {
            return $this->unauthorizedResponse('Missing token.');
        }

        // Try OAuth access token first
        $oauthToken = OauthAccessToken::with('user')
            ->where('token', hash('sha256', $token))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($oauthToken) {
            $oauthToken->touchLastUsed();
            $request->setUserResolver(fn () => $oauthToken->user);

            return $next($request);
        }

        // Fall back to MCP API key
        $user = User::findByMcpApiKey($token);

        if ($user) {
            $user->markMcpApiKeyAsUsed();
            $request->setUserResolver(fn () => $user);

            return $next($request);
        }

        return $this->unauthorizedResponse('Invalid token.');
    }

    protected function resolveBearerToken(Request $request): ?string
    {
        $token = $request->bearerToken();

        if (! $token) {
            $header = $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? null;

            // Last resort: getallheaders() can bypass cPanel's CGI stripping
            if (! $header && function_exists('getallheaders')) {
                $headers = array_change_key_case(getallheaders(), CASE_LOWER);
                $header  = $headers['authorization'] ?? null;
            }

            if ($header && str_starts_with($header, 'Bearer ')) {
                $token = substr($header, 7);
            }
        }

        return is_string($token) && $token !== '' ? $token : null;
    }

    protected function unauthorizedResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}
