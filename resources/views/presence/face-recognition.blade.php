@extends('layouts.app')

@section('title', 'Verifikasi Wajah')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Verifikasi Wajah</h2>
                    <p class="text-gray-600">Siswa: <strong>{{ $student->name }}</strong> · Card ID: <strong>{{ $student->card_id ?? 'Tidak tersedia' }}</strong></p>
                </div>
                <div class="text-right">
                    <a href="{{ route('kiosk') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-200">
                        Kembali ke Kios
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Langkah Verifikasi</h3>
                        <ol class="list-decimal list-inside text-gray-700 space-y-2">
                            <li>Periksa bahwa ini benar siswa yang melakukan presensi.</li>
                            <li>Tekan tombol <strong>Aktifkan Kamera</strong> untuk melihat preview.</li>
                            <li>Setelah wajah cocok, masukkan ID wajah atau gunakan sistem face recognition yang terhubung.</li>
                            <li>Tekan <strong>Konfirmasi Presensi</strong> untuk menyelesaikan.</li>
                        </ol>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <form method="POST" action="{{ route('kiosk.face-confirm') }}" class="space-y-5">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ID Wajah</label>
                                <input name="face_id" type="text" autocomplete="off" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Masukkan Face ID dari sistem kamera">
                                @error('face_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors">Konfirmasi Presensi</button>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Preview Kamera</h3>
                        <video id="camera-preview" class="w-full rounded-xl bg-black" autoplay muted playsinline></video>
                        <button id="start-camera" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition-colors">Aktifkan Kamera</button>
                    </div>

                    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Catatan Teknis</h3>
                        <p class="text-sm text-blue-700">Jika Raspberry Pi Anda sudah menjalankan face recognition, isi <strong>Face ID</strong> sesuai data siswa. Halaman ini juga bisa digunakan sebagai preview kamera untuk memastikan wajah siswa terlihat jelas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const startButton = document.getElementById('start-camera');
        const video = document.getElementById('camera-preview');

        startButton.addEventListener('click', async (event) => {
            event.preventDefault();

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Browser ini tidak mendukung kamera. Gunakan browser terbaru atau perangkat lain.');
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
            } catch (error) {
                alert('Tidak dapat mengakses kamera. Pastikan izin sudah diizinkan.');
            }
        });
    </script>
@endsection
