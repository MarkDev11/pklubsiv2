<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

/**
 * Math CAPTCHA fallback for when Cloudflare Turnstile is unreachable
 * (e.g., shared hosting that blocks outbound HTTPS to Cloudflare).
 *
 * Stores the expected answer in the session and validates user input
 * against it. Operations limited to single-digit addition/subtraction
 * for accessibility.
 */
class MathCaptchaService
{
    public const SESSION_KEY = 'math_captcha_answer';

    /**
     * Generate a fresh challenge and persist the expected answer in the session.
     *
     * @return array{question: string, a: int, b: int, op: string}
     */
    public function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $op = random_int(0, 1) === 0 ? '+' : '-';

        if ($op === '-' && $b > $a) {
            [$a, $b] = [$b, $a];
        }

        $answer = $op === '+' ? $a + $b : $a - $b;

        Session::put(self::SESSION_KEY, (string) $answer);

        return [
            'question' => "{$a} {$op} {$b}",
            'a' => $a,
            'b' => $b,
            'op' => $op,
        ];
    }

    /**
     * Validate the user's answer against the stored expected value.
     * One-shot: the stored answer is forgotten after a verification attempt
     * to prevent replay.
     */
    public function verify(?string $userAnswer): bool
    {
        $expected = Session::pull(self::SESSION_KEY);

        if ($expected === null || $userAnswer === null) {
            return false;
        }

        return trim($userAnswer) === $expected;
    }
}
