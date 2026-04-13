<?php

namespace App\Services;

use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class FonnteService
{
    protected string $token;

    protected int $rateLimit;

    protected string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
        $this->rateLimit = (int) config('services.fonnte.rate_limit', 10);
    }

    /**
     * Kirim pesan WhatsApp via Fonnte API (dengan throttle).
     *
     * @return array{status: bool, message: string, retry_after?: int}
     */
    public function send(string $target, string $message): array
    {
        if (empty($this->token)) {
            Log::warning('Fonnte token belum dikonfigurasi');

            return ['status' => false, 'message' => 'Token Fonnte belum dikonfigurasi'];
        }

        // Rate limiting: max N pesan per menit
        $key = 'fonnte-send';
        if (RateLimiter::tooManyAttempts($key, $this->rateLimit)) {
            $seconds = RateLimiter::availableIn($key);
            Log::info("Fonnte rate limited. Retry in {$seconds}s", ['target' => $target]);

            return [
                'status' => false,
                'message' => "Rate limited. Coba lagi dalam {$seconds} detik.",
                'retry_after' => $seconds,
            ];
        }

        RateLimiter::hit($key, 60); // decay 60 detik

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                Log::info('Fonnte message sent', ['target' => $target]);

                return ['status' => true, 'message' => 'Pesan terkirim'];
            }

            Log::error('Fonnte API Error', ['response' => $result]);

            return ['status' => false, 'message' => $result['reason'] ?? 'Gagal mengirim pesan'];
        } catch (\Exception $e) {
            Log::error('Fonnte Exception', ['message' => $e->getMessage()]);

            return ['status' => false, 'message' => 'Error koneksi ke Fonnte'];
        }
    }

    /**
     * Kirim pesan ke banyak nomor secara dicicil (batch, via queue).
     *
     * @param  array<int, string>  $targets
     */
    public function sendBatch(array $targets, string $message): void
    {
        foreach ($targets as $target) {
            // Dispatch each message as a queued job with delay
            SendWhatsAppMessage::dispatch($target, $message);
        }
    }
}
