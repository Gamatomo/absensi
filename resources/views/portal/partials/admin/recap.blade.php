@php
    $filterMonth = now()->format('Y-m');
@endphp
<div class="space-y-6" x-data="{ activeView:'students', searchQuery:'', filterMonth:'{{ $filterMonth }}' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3"><div class="p-2.5 bg-primary/10 rounded-lg"><x-icon name="file-spreadsheet" class="w-5 h-5 text-primary"/></div><div><h2 class="font-display">Rekap Absensi</h2><p class="text-sm text-muted-foreground">Ringkasan kehadiran siswa dan guru</p></div></div>
            <div class="flex gap-2">
                <button @click="activeView='students'" :class="activeView==='students' ? 'bg-primary text-primary-foreground' : 'bg-secondary'" class="px-4 py-2 rounded-lg text-sm flex items-center gap-2"><x-icon name="users" class="w-4 h-4"/>Siswa</button>
                <button @click="activeView='teachers'" :class="activeView==='teachers' ? 'bg-primary text-primary-foreground' : 'bg-secondary'" class="px-4 py-2 rounded-lg text-sm flex items-center gap-2"><x-icon name="graduation-cap" class="w-4 h-4"/>Guru</button>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchQuery" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari nama..."></div>
            <input type="month" x-model="filterMonth" class="px-4 py-3 border border-border rounded-lg bg-background">
        </div>
    </div>

    <div x-show="activeView==='students'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($students as $student)
            @php
                $records = collect($attendanceRecords)->filter(fn($r) => $r['studentId'] === $student['id'] && str_starts_with($r['timestamp'], substr($filterMonth, 0, 7)))->values();
                $total = $records->count();
                $present = $records->where('status', 'present')->count();
                $late = $records->where('status', 'late')->count();
                $rate = $total > 0 ? number_format((($present + $late) / $total) * 100, 1) : '0';
            @endphp
            <div class="bg-card border border-border rounded-lg p-5 shadow-sm" x-show="!searchQuery || '{{ strtolower($student['name'].' '.$student['department']) }}'.includes(searchQuery.toLowerCase())">
                <div class="flex justify-between mb-3"><div><h3 class="font-display">{{ $student['name'] }}</h3><p class="text-sm text-muted-foreground">{{ $student['department'] }}</p></div><span class="text-2xl font-display text-primary">{{ $rate }}%</span></div>
                <div class="grid grid-cols-3 gap-2 text-center text-sm">
                    <div class="p-2 bg-chart-3/10 rounded border border-chart-3/20"><p class="font-medium text-chart-3">{{ $present }}</p><p class="text-xs text-muted-foreground">Hadir</p></div>
                    <div class="p-2 bg-chart-4/10 rounded border border-chart-4/20"><p class="font-medium text-chart-4">{{ $late }}</p><p class="text-xs text-muted-foreground">Telat</p></div>
                    <div class="p-2 bg-chart-5/10 rounded border border-chart-5/20"><p class="font-medium text-chart-5">{{ $records->where('status','absent')->count() }}</p><p class="text-xs text-muted-foreground">Absen</p></div>
                </div>
            </div>
        @endforeach
    </div>

    <div x-show="activeView==='teachers'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($teachers as $teacher)
            <div class="bg-card border border-border rounded-lg p-5 shadow-sm" x-show="!searchQuery || '{{ strtolower($teacher['name'].' '.$teacher['subject']) }}'.includes(searchQuery.toLowerCase())">
                <h3 class="font-display">{{ $teacher['name'] }}</h3>
                <p class="text-sm text-muted-foreground mb-2">{{ $teacher['subject'] }}</p>
                <p class="text-xs text-muted-foreground">Data kehadiran guru mengikuti event absensi terintegrasi.</p>
            </div>
        @endforeach
    </div>
</div>
