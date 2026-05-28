@php
    $parent = $currentParent;
    $student = collect($students)->firstWhere('id', $parent['studentId'] ?? '') ?? $currentStudent;
    $studentRecords = collect($attendanceRecords)->where('studentId', $student['id'] ?? '');
    $studentLeave = collect($leaveRequests)->where('studentId', $student['id'] ?? '');
    $total = $studentRecords->count();
    $present = $studentRecords->where('status', 'present')->count();
    $attendanceRate = $total > 0 ? number_format(($present / $total) * 100, 1) : '0';
    $recentRecords = $studentRecords->sortByDesc('timestamp')->take(7);
    $todayName = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][now()->dayOfWeek];
    $todaySchedule = collect($schedules)->where('day', $todayName)->sortBy('startTime');
@endphp
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-6 text-primary-foreground shadow-md">
        <p class="text-primary-foreground/70 text-sm mb-1">Selamat Datang,</p>
        <h2 class="text-2xl font-display text-primary-foreground mb-1">{{ $parent['name'] ?? 'Orang Tua' }}</h2>
        <p class="text-primary-foreground/80 text-sm">{{ $parent['relationship'] ?? '' }} dari {{ $student['name'] ?? '-' }}</p>
    </div>
    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-card border border-border rounded-lg p-5 shadow-sm"><p class="text-sm text-muted-foreground">Tingkat Kehadiran Anak</p><h3 class="text-2xl font-display text-primary mt-1">{{ $attendanceRate }}%</h3></div>
        <div class="bg-card border border-border rounded-lg p-5 shadow-sm"><p class="text-sm text-muted-foreground">Hadir</p><h3 class="text-2xl font-display text-chart-3 mt-1">{{ $present }}</h3></div>
        <div class="bg-card border border-border rounded-lg p-5 shadow-sm"><p class="text-sm text-muted-foreground">Terlambat</p><h3 class="text-2xl font-display text-chart-4 mt-1">{{ $studentRecords->where('status','late')->count() }}</h3></div>
        <div class="bg-card border border-border rounded-lg p-5 shadow-sm"><p class="text-sm text-muted-foreground">Izin Pending</p><h3 class="text-2xl font-display text-chart-4 mt-1">{{ $studentLeave->where('status','pending')->count() }}</h3></div>
    </div>
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
            <h3 class="font-display mb-4 flex items-center gap-2"><x-icon name="calendar-check" class="w-5 h-5 text-primary"/>Absensi Terbaru</h3>
            <div class="space-y-2">
                @forelse($recentRecords as $record)
                <div class="flex justify-between p-3 bg-secondary/30 rounded-lg border border-border text-sm">
                    <span>{{ \Carbon\Carbon::parse($record['timestamp'])->locale('id')->isoFormat('D MMM YYYY') }}</span>
                    <span class="uppercase text-xs">{{ $record['status'] }}</span>
                </div>
                @empty
                <p class="text-sm text-muted-foreground">Belum ada data absensi.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
            <h3 class="font-display mb-4 flex items-center gap-2"><x-icon name="clock" class="w-5 h-5 text-emerald-600"/>Jadwal Hari Ini ({{ $todayName }})</h3>
            <div class="space-y-2">
                @forelse($todaySchedule as $s)
                <div class="p-3 bg-secondary/40 rounded-lg border border-border text-sm">
                    <p class="font-medium">{{ $s['subject'] }}</p>
                    <p class="text-muted-foreground">{{ $s['startTime'] }} – {{ $s['endTime'] }} · {{ $s['teacherName'] }}</p>
                </div>
                @empty
                <p class="text-sm text-muted-foreground">Tidak ada jadwal hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
