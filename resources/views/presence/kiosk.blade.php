@extends('layouts.app')

@section('title', 'Kios Presensi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Kios Presensi RFID</h2>
            <p class="text-gray-600 mb-4">Silakan minta siswa memindai kartu RFID mereka. Setelah kartu dikenali, sistem akan melanjutkan ke langkah verifikasi wajah.</p>

            <form method="POST" action="{{ route('kiosk.card-scan') }}" class="space-y-4" id="card-scan-form">
                @csrf
                <div>
                    <label for="card_id" class="block text-sm font-medium text-gray-700 mb-2">ID Kartu RFID</label>
                    <input id="card_id" name="card_id" type="text" autocomplete="off" autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Pindai kartu di sini" required>
                    @error('card_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors">Mulai Presensi</button>
            </form>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-blue-900 mb-2">Petunjuk</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                    <li>Siswa harus terdaftar dengan <strong>Card ID</strong> pada data siswa.</li>
                    <li>Setelah kartu terdeteksi, sistem akan membuka halaman konfirmasi wajah.</li>
                    <li>Jika kartu tidak dikenali, periksa kembali data siswa di halaman admin.</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const cardInput = document.getElementById('card_id');
        const cardForm = document.getElementById('card-scan-form');
        let typingTimeout;

        cardInput.addEventListener('input', () => {
            clearTimeout(typingTimeout);
            const value = cardInput.value.trim();

            if (value.length >= 4) {
                typingTimeout = setTimeout(() => {
                    cardForm.submit();
                }, 300);
            }
        });

        cardInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                cardForm.submit();
            }
        });
    </script>
@endsection
