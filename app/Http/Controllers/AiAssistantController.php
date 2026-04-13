<?php

namespace App\Http\Controllers;

use App\Http\Requests\AiChatRequest;
use App\Services\GroqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class AiAssistantController extends Controller
{
    public function chat(AiChatRequest $request, GroqService $groq): JsonResponse
    {
        $user = $this->authenticatedUser();
        $key = 'ai-chat:'.$user->id;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'reply' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $response = $groq->chat(
            $request->message,
            $request->history ?? []
        );

        return response()->json([
            'reply' => $response,
        ]);
    }
}
