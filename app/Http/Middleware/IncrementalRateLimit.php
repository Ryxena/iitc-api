<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Incremental Rate Limit Middleware
 *
 * Tracks per-IP violations and increases the block duration on each strike.
 *
 * Window buckets (per action key, e.g. 'login' or 'register'):
 *   1st block  →  1 minute
 *   2nd block  →  5 minutes
 *   3rd block  →  15 minutes
 *   4th block  →  60 minutes
 *   5th+ block →  24 hours
 *
 * Cache keys (keyed by IP + action):
 *   irl:{action}:attempts:{ip}  – number of requests in the current window
 *   irl:{action}:violations:{ip} – how many times this IP has been blocked before
 *   irl:{action}:blocked:{ip}   – set while the IP is under an active block
 */
class IncrementalRateLimit
{
    /**
     * Penalty durations in seconds indexed by violation count (0-based).
     */
    private const PENALTIES = [
        0 => 60,   // 1 minute
        1 => 300,   // 5 minutes
        2 => 900,   // 15 minutes
        3 => 3600,   // 60 minutes
        4 => 86400,   // 24 hours
    ];

    /**
     * Max requests allowed per minute before a violation is recorded.
     * Override per action via constructor parameter.
     */
    private const LIMITS = [
        'login' => 5,
        'register' => 3,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  string  $action  The action key ('login' | 'register')
     */
    public function handle(Request $request, Closure $next, string $action = 'login'): Response
    {
        $ip = $request->ip();
        $blockKey = "irl:{$action}:blocked:{$ip}";
        $attemptsKey = "irl:{$action}:attempts:{$ip}";
        $violationsKey = "irl:{$action}:violations:{$ip}";

        // ── 1. Check if currently blocked ──────────────────────────────────
        $blockedUntil = Cache::get($blockKey);
        if ($blockedUntil !== null) {
            $remainingSeconds = max(0, $blockedUntil - now()->timestamp);

            return $this->blockedResponse($remainingSeconds);
        }

        // ── 2. Increment attempt counter (1-minute sliding window) ─────────
        $maxAttempts = self::LIMITS[$action] ?? 5;
        $windowKey = "irl:{$action}:window_start:{$ip}";

        $windowStart = Cache::get($windowKey);
        if ($windowStart === null) {
            // Fresh window: store the start timestamp, expires in 60s
            Cache::put($windowKey, now()->timestamp, 60);
            Cache::put($attemptsKey, 1, 60);
            $attempts = 1;
        } else {
            $elapsed = now()->timestamp - $windowStart;
            $remainingInWindow = max(1, 60 - $elapsed);
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, $remainingInWindow);
        }

        // ── 3. If attempts exceeded, record violation and block ────────────
        if ($attempts > $maxAttempts) {
            $violations = Cache::get($violationsKey, 0);
            $penaltyIndex = min($violations, count(self::PENALTIES) - 1);
            $penaltySeconds = self::PENALTIES[$penaltyIndex];

            // Increment violations (persist 24h so repeated offenders escalate)
            Cache::put($violationsKey, $violations + 1, 86400);

            // Store absolute timestamp so remaining time is always accurate
            $blockedUntilTs = now()->timestamp + $penaltySeconds;
            Cache::put($blockKey, $blockedUntilTs, $penaltySeconds);

            // Reset the attempts window
            Cache::forget($attemptsKey);
            Cache::forget($windowKey);

            return $this->blockedResponse($penaltySeconds);
        }

        return $next($request);
    }

    /**
     * Build a consistent 429 JSON response.
     */
    private function blockedResponse(int $remainingSeconds): Response
    {
        $minutes = (int) ceil($remainingSeconds / 60);
        $label = $minutes <= 1
            ? '1 menit'
            : "{$minutes} menit";

        return response()->json([
            'success' => false,
            'message' => "Terlalu banyak percobaan, harap coba lagi dalam {$label}",
            'retry_after' => $remainingSeconds,
        ], 429);
    }
}
