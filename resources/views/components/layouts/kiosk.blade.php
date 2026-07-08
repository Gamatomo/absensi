@props(['title', 'subtitle', 'accent' => 'primary'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Sistem Presensi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@0.487.0/dist/umd/lucide.min.js"></script>
    <style>
        @keyframes kiosk-pulse-ring {
            0% { transform: scale(0.95); opacity: 0.6; }
            70% { transform: scale(1.15); opacity: 0; }
            100% { transform: scale(1.15); opacity: 0; }
        }
        @keyframes kiosk-scan {
            0%, 100% { top: 12%; opacity: 0.4; }
            50% { top: 82%; opacity: 1; }
        }
        @keyframes kiosk-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .kiosk-pulse-ring { animation: kiosk-pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .kiosk-scan-line { animation: kiosk-scan 2.8s ease-in-out infinite; }
        .kiosk-float { animation: kiosk-float 3s ease-in-out infinite; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-background text-foreground antialiased overflow-x-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full blur-3xl {{ $accent === 'face' ? 'bg-emerald-500/10' : 'bg-blue-500/10' }}"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[32rem] w-[32rem] rounded-full bg-secondary/60 blur-3xl"></div>
    </div>

    <div class="min-h-screen flex flex-col">
        <header class="border-b border-border/80 bg-card/80 backdrop-blur-md">
            <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="bg-primary p-2.5 rounded-xl shadow-sm">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-primary-foreground"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-muted-foreground font-medium">Sistem Presensi</p>
                        <h1 class="font-display text-lg leading-tight">{{ $title }}</h1>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium" x-data x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></p>
                    <p class="text-xs text-muted-foreground font-mono" x-data="{ t: '' }" x-init="setInterval(() => t = new Date().toLocaleTimeString('id-ID'), 1000); t = new Date().toLocaleTimeString('id-ID')" x-text="t"></p>
                </div>
            </div>
        </header>

        @isset($sidebar)
        {{-- Two-column layout: Scanner (left) + Attendance List (right) --}}
        <main class="flex-1 grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 p-4 sm:p-6 w-full overflow-hidden">
            {{-- Left: Scanner Area --}}
            <div class="flex flex-col items-center justify-center">
                <div class="w-full max-w-lg mx-auto">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl sm:text-3xl font-display tracking-tight">{{ $subtitle }}</h2>
                        <p class="text-sm text-muted-foreground mt-2">Posisikan diri Anda di depan perangkat kiosk</p>
                    </div>
                    {{ $slot }}
                </div>
            </div>
            {{-- Right: Live Attendance List --}}
            <div class="min-h-0 lg:max-h-[calc(100vh-12rem)] overflow-y-auto">
                {{ $sidebar }}
            </div>
        </main>
        @else
        {{-- Single-column layout (no sidebar) --}}
        <main class="flex-1 flex items-center justify-center p-4 sm:p-8">
            <div class="w-full max-w-lg">
                <div class="text-center mb-6">
                    <h2 class="text-2xl sm:text-3xl font-display tracking-tight">{{ $subtitle }}</h2>
                    <p class="text-sm text-muted-foreground mt-2">Posisikan diri Anda di depan perangkat kiosk</p>
                </div>
                {{ $slot }}
            </div>
        </main>
        @endisset

        <footer class="border-t border-border/80 bg-card/60 backdrop-blur-sm py-4">
            <div class="max-w-5xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-muted-foreground">
                <p>Butuh bantuan? Hubungi Admin Portal Presensi</p>
                <p class="font-medium text-foreground/80">Kiosk Mode · {{ config('app.name', 'Attendance') }}</p>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => window.lucide?.createIcons());
        document.addEventListener('alpine:initialized', () => window.lucide?.createIcons());
    </script>
    @stack('scripts')
</body>
</html>
