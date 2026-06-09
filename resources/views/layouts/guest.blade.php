<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — Sistem PKL UBSI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pkl_logo.png') }}">
    @if(! (app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false)))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer
                onerror="window.dispatchEvent(new CustomEvent('turnstile:failed'))"></script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        /* Force light scheme on auth pages — abaikan toggle dark mode */
        html, body { color-scheme: light; }

        /* Background gradient: biru tua → biru muda */
        .login-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 35%, #2563eb 65%, #60a5fa 100%);
        }

        /* Hexagon texture overlay */
        .bg-hexagon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100' viewBox='0 0 56 100'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1' stroke-opacity='0.12'%3E%3Cpath d='M28 66L0 50L0 16L28 0L56 16L56 50L28 66L28 100'/%3E%3Cpath d='M28 0L28 34L0 50L0 84L28 100L56 84L56 50L28 34'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 56px 100px;
        }

        /* Login card */
        .login-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06),
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Input focus */
        .login-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Role Card */
        .role-card {
            transition: all 0.2s ease;
        }

        .role-card:hover {
            border-color: #3b82f6;
        }

        .role-card.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        /* Animation */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-slide-up {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .split-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="font-sans antialiased login-bg min-h-screen flex items-center justify-center p-4 lg:p-8">

    {{-- Hexagon Texture Overlay --}}
    <div class="fixed inset-0 -z-10 bg-hexagon pointer-events-none"></div>

    {{-- Main Container --}}
    <div class="w-full max-w-6xl animate-slide-up relative z-10">
        {{ $slot }}
    </div>

    @stack('scripts')

</body>
</html>
