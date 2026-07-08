@props(['title' => 'Sistem Presensi', 'subtitle' => ''])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-background text-foreground">
    {{ $slot }}

    <script src="https://unpkg.com/lucide@0.487.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @stack('scripts')
    <script>
        document.addEventListener('alpine:initialized', () => {
            if (window.lucide) window.lucide.createIcons();
        });
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) window.lucide.createIcons();
        });
    </script>

    @if (session('status') || session('success') || session('error') || $errors->any())
        <div x-data="{
                show: true,
                type: '{{ session('error') || $errors->any() ? 'error' : 'success' }}',
                message: '{{ session('status') === 'password-updated' ? 'Password berhasil diubah!' : (session('status') === 'profile-updated' ? 'Profil berhasil diperbarui!' : (session('success') ?? (session('error') ?? 'Terdapat kesalahan pada input Anda.'))) }}'
            }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed bottom-4 right-4 z-50 flex items-center p-4 space-x-3 w-full max-w-sm bg-card border border-border rounded-lg shadow-lg"
            style="display: none;"
        >
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                </template>
                <template x-if="type === 'error'">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                </template>
            </div>
            <div class="flex-1 w-0">
                <p class="text-sm font-medium text-foreground" x-text="message"></p>
            </div>
            <div class="flex-shrink-0">
                <button @click="show = false" type="button" class="inline-flex rounded-md bg-card text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <span class="sr-only">Tutup</span>
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <script>
            document.addEventListener('alpine:initialized', () => {
                if (window.lucide) window.lucide.createIcons();
            });
        </script>
    @endif
</body>
</html>
