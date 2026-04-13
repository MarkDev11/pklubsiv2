<?php

return [
    'ddos' => [
        'burst_max_attempts' => (int) env('DDOS_BURST_MAX_ATTEMPTS', 40),
        'burst_decay_seconds' => (int) env('DDOS_BURST_DECAY_SECONDS', 10),
        'minute_max_attempts' => (int) env('DDOS_MINUTE_MAX_ATTEMPTS', 240),
        'minute_decay_seconds' => (int) env('DDOS_MINUTE_DECAY_SECONDS', 60),
    ],
];
