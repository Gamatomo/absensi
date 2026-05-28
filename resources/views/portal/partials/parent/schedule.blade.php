@php
    $student = collect($students)->firstWhere('id', $currentParent['studentId'] ?? '') ?? $currentStudent;
@endphp
<div class="bg-card border border-border rounded-lg shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-emerald-500/10 rounded-lg"><x-icon name="clock" class="w-5 h-5 text-emerald-600"/></div>
            <div><h2 class="font-display">Jadwal Pelajaran</h2><p class="text-sm text-muted-foreground">Kelas {{ $student['department'] ?? '' }}</p></div>
        </div>
    </div>
    <div class="p-6">
        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $day)
            @php $daySchedules = collect($schedules)->where('day', $day)->sortBy('startTime'); @endphp
            @if($daySchedules->isNotEmpty())
            <div class="mb-4">
                <h4 class="text-sm font-medium text-muted-foreground mb-2 uppercase tracking-wide">{{ $day }}</h4>
                <div class="space-y-2">
                    @foreach($daySchedules as $s)
                    <div class="flex items-center gap-4 p-3 bg-secondary/40 rounded-lg border border-border">
                        <div class="text-sm font-mono text-muted-foreground w-28 shrink-0">{{ $s['startTime'] }} – {{ $s['endTime'] }}</div>
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $s['subject'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $s['teacherName'] }} · {{ $s['className'] }}</p>
                        </div>
                        @if($s['room'])<span class="text-xs text-muted-foreground shrink-0">{{ $s['room'] }}</span>@endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
