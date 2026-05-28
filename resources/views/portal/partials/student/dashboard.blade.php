@php
    $student = $currentStudent;
    $myAttendance = collect($attendanceRecords)->where('studentId', $student['id'] ?? '');
    $totalDays = $myAttendance->count();
    $presentCount = $myAttendance->where('status', 'present')->count();
    $lateCount = $myAttendance->where('status', 'late')->count();
    $absentCount = $myAttendance->where('status', 'absent')->count();
    $attendanceRate = $totalDays > 0 ? number_format((($presentCount + $lateCount) / $totalDays) * 100, 1) : '0';
@endphp
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 text-primary-foreground shadow-lg border border-primary/20">
        <div class="flex items-center gap-4">
            <div class="bg-primary-foreground/20 p-4 rounded-full border border-primary-foreground/30"><x-icon name="user" class="w-12 h-12 text-primary-foreground"/></div>
            <div><h2 class="text-primary-foreground font-display mb-1">Selamat Datang, {{ $student['name'] ?? 'Siswa' }}</h2><p class="text-primary-foreground/80 text-sm">Dashboard Presensi Pribadi</p></div>
        </div>
    </div>
    <div class="bg-card rounded-xl border border-border shadow-sm p-6">
        <h3 class="mb-4 flex items-center gap-2 font-display"><x-icon name="id-card" class="w-5 h-5 text-primary"/>Informasi Profil</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border"><x-icon name="mail" class="w-5 h-5 text-muted-foreground"/><div><p class="text-xs text-muted-foreground">Email</p><p>{{ $student['email'] ?? '-' }}</p></div></div>
            <div class="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border"><x-icon name="building-2" class="w-5 h-5 text-muted-foreground"/><div><p class="text-xs text-muted-foreground">Jurusan</p><p>{{ $student['department'] ?? '-' }}</p></div></div>
            <div class="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border"><x-icon name="calendar" class="w-5 h-5 text-muted-foreground"/><div><p class="text-xs text-muted-foreground">Tanggal Daftar</p><p>{{ isset($student['enrolledDate']) ? \Carbon\Carbon::parse($student['enrolledDate'])->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</p></div></div>
            <div class="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border"><x-icon name="id-card" class="w-5 h-5 text-muted-foreground"/><div><p class="text-xs text-muted-foreground">NIS/NISN</p><p>{{ $student['nisn'] ?? $student['id'] ?? '-' }}</p></div></div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-card rounded-xl border border-border shadow-sm p-6"><div class="bg-primary/10 p-3 rounded-lg border border-primary/20 w-fit mb-4"><x-icon name="trending-up" class="w-6 h-6 text-primary"/></div><p class="text-sm text-muted-foreground mb-1">Tingkat Kehadiran</p><h3 class="text-primary text-2xl font-display">{{ $attendanceRate }}%</h3></div>
        <div class="bg-card rounded-xl border border-border shadow-sm p-6"><div class="bg-chart-3/10 p-3 rounded-lg border border-chart-3/20 w-fit mb-4"><x-icon name="check-circle" class="w-6 h-6 text-chart-3"/></div><p class="text-sm text-muted-foreground mb-1">Hadir</p><h3 class="text-chart-3 text-2xl font-display">{{ $presentCount }}</h3><p class="text-xs text-muted-foreground mt-1">dari {{ $totalDays }} hari</p></div>
        <div class="bg-card rounded-xl border border-border shadow-sm p-6"><div class="bg-chart-4/10 p-3 rounded-lg border border-chart-4/20 w-fit mb-4"><x-icon name="clock" class="w-6 h-6 text-chart-4"/></div><p class="text-sm text-muted-foreground mb-1">Terlambat</p><h3 class="text-chart-4 text-2xl font-display">{{ $lateCount }}</h3></div>
        <div class="bg-card rounded-xl border border-border shadow-sm p-6"><div class="bg-chart-5/10 p-3 rounded-lg border border-chart-5/20 w-fit mb-4"><x-icon name="x-circle" class="w-6 h-6 text-chart-5"/></div><p class="text-sm text-muted-foreground mb-1">Tidak Hadir</p><h3 class="text-chart-5 text-2xl font-display">{{ $absentCount }}</h3></div>
    </div>
    <div class="bg-card rounded-xl border border-border shadow-sm p-6">
        <h3 class="mb-4 flex items-center gap-2 font-display"><x-icon name="clipboard-list" class="w-5 h-5 text-primary"/>Peraturan Tata Cara Absen</h3>
        <div class="space-y-4">
            <div class="p-4 bg-primary/5 rounded-lg border border-primary/20 text-sm text-muted-foreground">
                <h4 class="flex items-center gap-2 mb-3 text-primary"><x-icon name="check-circle" class="w-4 h-4"/>Waktu Kehadiran</h4>
                <ul class="space-y-2"><li>• Siswa wajib hadir sebelum jam <strong>07:30 WIB</strong></li><li>• Keterlambatan di atas jam 07:30 dicatat sebagai <strong>terlambat</strong></li></ul>
            </div>
            <div class="p-4 bg-secondary/50 rounded-lg border border-border text-sm text-muted-foreground">
                <h4 class="mb-3 flex items-center gap-2"><x-icon name="scan-face" class="w-4 h-4 text-primary"/>Metode Absensi</h4>
                <p>Pengenalan wajah dan kartu RFID di pintu masuk sekolah.</p>
            </div>
        </div>
    </div>
</div>
