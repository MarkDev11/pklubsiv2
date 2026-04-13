<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — Sistem PKL UBSI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pkl_logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if(! (app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false)))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        .login-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 35%, #3b82f6 65%, #60a5fa 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated floating icons */
        .float-icon {
            position: absolute;
            opacity: 0.12;
            animation: floatUp linear infinite;
            pointer-events: none;
            font-size: 2rem;
        }
        @keyframes floatUp {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.15; }
            90% { opacity: 0.15; }
            100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
        }

        /* Grid animation */
        .grid-bg {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridScroll 20s linear infinite;
        }
        @keyframes gridScroll {
            0% { background-position: 0 0; }
            100% { background-position: 60px 60px; }
        }

        /* Glow pulse */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: glowPulse 6s ease-in-out infinite alternate;
        }
        @keyframes glowPulse {
            0% { opacity: 0.2; transform: scale(1); }
            100% { opacity: 0.35; transform: scale(1.2); }
        }

        /* Card glass */
        .login-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.08) inset,
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 80px rgba(59, 130, 246, 0.1);
        }

        /* Input focus glow */
        .login-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), 0 0 20px rgba(59, 130, 246, 0.1);
        }

        /* Role card */
        .role-card {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .role-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.15) !important;
        }
        .role-card.active {
            background: rgba(255, 255, 255, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2), 0 0 0 2px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        /* Particle line */
        .particle-line {
            position: absolute;
            width: 1px;
            height: 100px;
            background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: particleFall linear infinite;
        }
        @keyframes particleFall {
            0% { transform: translateY(-100px); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0; }
        }
    </style>
</head>
<body class="font-sans antialiased login-bg min-h-screen flex items-center justify-center p-4">

    {{-- Animated Background Layer --}}
    <div class="fixed inset-0 -z-10">
        {{-- Grid --}}
        <div class="absolute inset-0 grid-bg"></div>

        {{-- Glow Orbs --}}
        <div class="glow-orb w-96 h-96 bg-blue-600" style="top: 10%; left: -10%;"></div>
        <div class="glow-orb w-80 h-80 bg-indigo-500" style="bottom: 5%; right: -5%; animation-delay: 3s;"></div>
        <div class="glow-orb w-64 h-64 bg-blue-400" style="top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: 1.5s;"></div>

        {{-- Floating PKL-themed icons --}}
        <div class="float-icon" style="left: 5%; animation-duration: 18s; animation-delay: 0s;"><i class="fa-solid fa-briefcase"></i></div>
        <div class="float-icon" style="left: 15%; animation-duration: 22s; animation-delay: 2s;"><i class="fa-solid fa-chart-bar"></i></div>
        <div class="float-icon" style="left: 25%; animation-duration: 16s; animation-delay: 5s;"><i class="fa-solid fa-graduation-cap"></i></div>
        <div class="float-icon" style="left: 35%; animation-duration: 24s; animation-delay: 1s;"><i class="fa-solid fa-clipboard-list"></i></div>
        <div class="float-icon" style="left: 45%; animation-duration: 20s; animation-delay: 4s;"><i class="fa-solid fa-building"></i></div>
        <div class="float-icon" style="left: 55%; animation-duration: 19s; animation-delay: 3s;"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="float-icon" style="left: 65%; animation-duration: 23s; animation-delay: 6s;"><i class="fa-solid fa-desktop"></i></div>
        <div class="float-icon" style="left: 75%; animation-duration: 17s; animation-delay: 2s;"><i class="fa-solid fa-file-lines"></i></div>
        <div class="float-icon" style="left: 85%; animation-duration: 21s; animation-delay: 7s;"><i class="fa-solid fa-laptop-code"></i></div>
        <div class="float-icon" style="left: 95%; animation-duration: 25s; animation-delay: 0.5s;"><i class="fa-solid fa-bullseye"></i></div>

        {{-- Particle Lines --}}
        <div class="particle-line" style="left: 10%; animation-duration: 8s; animation-delay: 0s;"></div>
        <div class="particle-line" style="left: 30%; animation-duration: 12s; animation-delay: 2s;"></div>
        <div class="particle-line" style="left: 50%; animation-duration: 10s; animation-delay: 4s;"></div>
        <div class="particle-line" style="left: 70%; animation-duration: 9s; animation-delay: 1s;"></div>
        <div class="particle-line" style="left: 90%; animation-duration: 11s; animation-delay: 3s;"></div>
    </div>

    {{-- Login Card --}}
    <div class="w-full max-w-md animate-slide-up relative z-10">
        {{ $slot }}
    </div>

    @stack('scripts')

</body>
</html>
