@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Navigation Tabs -->
        <div class="flex gap-2 border-b border-gray-200">
            <button class="nav-tab active px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-medium transition-colors" data-tab="dashboard">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Beranda
                </span>
            </button>
            <button class="nav-tab px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium transition-colors" data-tab="upload-students">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Unggah Data Siswa
                </span>
            </button>
            <button class="nav-tab px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium transition-colors" data-tab="upload-attendance">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Tambah Siswa
                </span>
            </button>
            <button class="nav-tab px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium transition-colors" data-tab="students">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 7H9"></path></svg>
                    Data Siswa
                </span>
            </button>
            <a href="{{ route('kiosk') }}" class="inline-flex items-center px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium transition-colors rounded-t-lg">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buka Kios Presensi
                </span>
            </a>
            <button class="nav-tab px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium transition-colors" data-tab="history">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Riwayat
                </span>
            </button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Stats Cards -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Siswa</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalStudents }}</p>
                        </div>
                        <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 6h-6a3 3 0 00-3 3v7a3 3 0 003 3h6a3 3 0 003-3V9a3 3 0 00-3-3z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Hadir Hari Ini</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $todayPresent }}</p>
                        </div>
                        <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Terlambat Hari Ini</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $todayLate }}</p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Absen Hari Ini</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $todayAbsent }}</p>
                        </div>
                        <svg class="w-12 h-12 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Presensi</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 text-sm mb-1">Total Presensi Tercatat</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalRecords }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 text-sm mb-1">Rata-rata Kehadiran</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @if($totalStudents > 0)
                                {{ round(($todayPresent / $totalStudents) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 text-sm mb-1">Siswa Terdaftar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Students Tab -->
        <div id="upload-students" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Unggah Data Siswa</h2>
                
                <form method="POST" action="{{ route('students.upload') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File CSV</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors cursor-pointer" id="dropzone">
                            <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" class="hidden" required>
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"></path></svg>
                            <p class="text-gray-600 font-medium">Tarik & lepas file CSV di sini</p>
                            <p class="text-gray-500 text-sm">atau klik untuk memilih file</p>
                            <p class="text-gray-400 text-xs mt-2">Format: Nama, Email, Departemen, Card ID (opsional), Face ID (opsional)</p>
                        </div>
                        @error('csv_file')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Unggah Siswa
                    </button>
                </form>

                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-3">Format File CSV:</h3>
                    <pre class="text-xs text-blue-800 overflow-x-auto">Nama,Email,Departemen,Card ID,Face ID
John Doe,john@example.com,Engineering,CARD001,FACE001
Jane Smith,jane@example.com,Science,CARD002,FACE002</pre>
                </div>
            </div>
        </div>

        <!-- Add Student Form Tab -->
        <div id="upload-attendance" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Tambah Siswa Baru</h2>
                
                <form method="POST" action="{{ route('students.store') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa *</label>
                        <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen *</label>
                        <input type="text" name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        @error('department')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Kartu</label>
                        <input type="text" name="card_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Wajah</label>
                        <input type="text" name="face_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pendaftaran</label>
                        <input type="date" name="enrolled_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Tambah Siswa
                    </button>
                </form>
            </div>
        </div>

        <!-- Students List Tab -->
        <div id="students" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Data Siswa ({{ $students->count() }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Departemen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID Kartu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->department }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->card_id ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="{{ route('students.delete', $student->id) }}" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-600">Tidak ada data siswa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Attendance History Tab -->
        <div id="history" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Riwayat Presensi ({{ $attendanceRecords->count() }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($attendanceRecords as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $record->student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $record->timestamp->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($record->method === 'face')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Wajah</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Kartu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($record->status === 'present')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span>
                                        @elseif($record->status === 'late')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Terlambat</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Absen</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $record->location ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form method="POST" action="{{ route('attendance.delete', $record->id) }}" onsubmit="return confirm('Yakin ingin menghapus presensi ini?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-600">Tidak ada data presensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching logic
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Remove active class from all buttons
                document.querySelectorAll('.nav-tab').forEach(button => {
                    button.classList.remove('border-blue-600', 'text-blue-600');
                    button.classList.add('border-transparent', 'text-gray-600');
                });
                
                // Show selected tab
                document.getElementById(tabName).classList.remove('hidden');
                
                // Add active class to clicked button
                this.classList.remove('border-transparent', 'text-gray-600');
                this.classList.add('border-blue-600', 'text-blue-600');
            });
        });

        // File drop zone
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('csv_file');
        
        if (dropzone) {
            dropzone.addEventListener('click', () => fileInput.click());
            
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-400', 'bg-blue-50');
            });
            
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-blue-400', 'bg-blue-50');
            });
            
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-400', 'bg-blue-50');
                fileInput.files = e.dataTransfer.files;
            });
        }
    </script>
@endsection
