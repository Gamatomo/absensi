@props(['title' => null])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Attendance System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
    <header class="border-b border-border bg-card shadow-sm">
        <div class="container mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl tracking-tight">Sistem Presensi</h1>
                    <p class="text-sm text-muted-foreground">RFID + Face Verification</p>
                </div>
                <nav class="hidden md:flex items-center gap-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Beranda</a>
                    <a href="{{ route('students.index') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Siswa</a>
                    <a href="{{ route('teachers.index') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Guru</a>
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Absensi</a>
                    <a href="{{ route('rfid-registration.index') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">RFID</a>
                    <a href="{{ route('face-profiles.index') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Face</a>
                    @auth
                        <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-lg hover:bg-secondary">Profile</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-8">
        {{ $slot }}
    </main>
</body>
</html>
