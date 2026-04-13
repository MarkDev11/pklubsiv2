<?php

namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60]; // retry with increasing delay

    public function __construct(
        public string $target,
        public string $message
    ) {}

    /**
     * Rate limit middleware: max 10 jobs per minute.
     *
     * @return array<int, RateLimited>
     */
    public function middleware(): array
    {
        return [new RateLimited('fonnte')];
    }

    public function handle(FonnteService $fonnte): void
    {
        $result = $fonnte->send($this->target, $this->message);

        if (! $result['status'] && isset($result['retry_after'])) {
            $this->release($result['retry_after']);
        }
    }
}
