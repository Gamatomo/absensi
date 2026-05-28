@php
    $student = $currentStudent;
    $myAttendance = collect($attendanceRecords)->where('studentId', $student['id'] ?? '');
@endphp
<div class="space-y-6" x-data="{ filterMonth: '{{ now()->format('Y-m') }}', filterStatus: 'all' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-4 flex items-center gap-2"><x-icon name="clipboard-list" class="w-5 h-5 text-primary"/>Rekap Absensi Saya</h2>
        <div class="flex flex-col sm:flex-row gap-4">
            <input type="month" x-model="filterMonth" class="px-4 py-3 border border-border rounded-lg bg-background">
            <select x-model="filterStatus" class="px-4 py-3 border border-border rounded-lg bg-background"><option value="all">Semua Status</option><option value="present">Hadir</option><option value="late">Terlambat</option><option value="absent">Tidak Hadir</option></select>
        </div>
    </div>
    <div class="space-y-3">
        @foreach($myAttendance as $record)
        <div class="bg-card border border-border rounded-lg p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3"
             data-month="{{ \Carbon\Carbon::parse($record['timestamp'])->format('Y-m') }}"
             data-status="{{ $record['status'] }}"
             x-show="filterMonth === '{{ \Carbon\Carbon::parse($record['timestamp'])->format('Y-m') }}' && (filterStatus==='all'||filterStatus==='{{ $record['status'] }}')">
            <div>
                <p class="font-medium">{{ \Carbon\Carbon::parse($record['timestamp'])->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                <p class="text-sm text-muted-foreground">{{ $record['location'] ?? 'Lokasi tidak tercatat' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-1 rounded border {{ $record['method']==='face' ? 'bg-primary/10 text-primary' : 'bg-accent/10 text-accent' }}">{{ $record['method']==='face' ? 'Wajah' : 'Kartu' }}</span>
                <span class="text-xs px-3 py-1 rounded-full uppercase {{ $record['status']==='present'?'bg-chart-3/10 text-chart-3':($record['status']==='late'?'bg-chart-4/10 text-chart-4':'bg-chart-5/10 text-chart-5') }}">{{ $record['status'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
