<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi - @yield('title', 'Pengelolaan Kehadiran Siswa')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="border-b border-gray-200 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-600 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Sistem Presensi</h1>
                        <p class="text-sm text-gray-600">Pengelolaan Kehadiran Siswa</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-gray-100 px-4 py-2 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-md border border-gray-200">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Pengenalan Wajah</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-md border border-gray-200">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m-4 4v2m4 4v2M9 5h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Akses Kartu</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Success Message -->
    @if ($message = Session::get('success'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ $message }}
            </div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ $message }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white mt-12">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <p class="text-center text-sm text-gray-600">Sistem Presensi © 2024. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
