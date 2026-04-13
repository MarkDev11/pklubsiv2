<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class DdosProtection
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip() ?? 'unknown';
        $routeFingerprint = $request->route()?->getName() ?? $request->path();

        $burstKey = 'ddos:burst:'.$ip.':'.$routeFingerprint;
        $minuteKey = 'ddos:minute:'.$ip;

        $burstMaxAttempts = (int) config('security.ddos.burst_max_attempts', 40);
        $burstDecaySeconds = (int) config('security.ddos.burst_decay_seconds', 10);
        $minuteMaxAttempts = (int) config('security.ddos.minute_max_attempts', 240);
        $minuteDecaySeconds = (int) config('security.ddos.minute_decay_seconds', 60);

        if (RateLimiter::tooManyAttempts($burstKey, $burstMaxAttempts)) {
            $retryAfter = RateLimiter::availableIn($burstKey);

            return response('Too many requests (burst protection).', 429)
                ->header('Retry-After', (string) $retryAfter);
        }

        if (RateLimiter::tooManyAttempts($minuteKey, $minuteMaxAttempts)) {
            $retryAfter = RateLimiter::availableIn($minuteKey);

            return response('Too many requests (DDoS protection).', 429)
                ->header('Retry-After', (string) $retryAfter);
        }

        RateLimiter::hit($burstKey, $burstDecaySeconds);
        RateLimiter::hit($minuteKey, $minuteDecaySeconds);

        return $next($request);
    }
}
