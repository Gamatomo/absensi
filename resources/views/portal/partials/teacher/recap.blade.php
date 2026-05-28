@php $teacher = $currentTeacher; @endphp
<div class="bg-card border border-border rounded-lg p-6 shadow-sm">
    <h2 class="font-display mb-4 flex items-center gap-2"><x-icon name="clipboard-list" class="w-5 h-5 text-primary"/>Rekap Absensi Guru</h2>
    <p class="text-sm text-muted-foreground mb-4">Rekap kehadiran untuk <strong>{{ $teacher['name'] ?? 'Guru' }}</strong> ({{ $teacher['id'] ?? '-' }}).</p>
    <div class="space-y-3">
        @forelse(collect($attendanceRecords)->take(10) as $record)
        <div class="flex justify-between items-center p-3 bg-secondary/30 rounded-lg border border-border text-sm">
            <span>{{ \Carbon\Carbon::parse($record['timestamp'])->locale('id')->isoFormat('D MMM YYYY HH:mm') }}</span>
            <span class="uppercase text-xs px-2 py-1 rounded {{ $record['status']==='present'?'bg-chart-3/10 text-chart-3':'bg-chart-4/10 text-chart-4' }}">{{ $record['status'] }}</span>
        </div>
        @empty
        <p class="text-sm text-muted-foreground">Belum ada data rekap absensi guru.</p>
        @endforelse
    </div>
</div>
