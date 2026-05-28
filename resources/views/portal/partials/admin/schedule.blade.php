<div class="space-y-6" x-data="{ searchTerm:'', filterDay:'all' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6"><div class="p-2.5 bg-emerald-500/10 rounded-lg"><x-icon name="clock" class="w-5 h-5 text-emerald-600"/></div><div><h2 class="font-display">Jadwal Pelajaran</h2><p class="text-sm text-muted-foreground">{{ count($schedules) }} jadwal</p></div></div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari mata pelajaran, kelas, guru..."></div>
            <select x-model="filterDay" class="px-4 py-3 border border-border rounded-lg bg-background"><option value="all">Semua Hari</option>@foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach</select>
        </div>
    </div>
    <div class="bg-card border border-border rounded-lg overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-secondary/50 border-b border-border"><tr class="text-left"><th class="p-4">Hari</th><th class="p-4">Waktu</th><th class="p-4">Mapel</th><th class="p-4">Kelas</th><th class="p-4">Guru</th><th class="p-4">Ruang</th></tr></thead>
            <tbody>
            @foreach($schedules as $schedule)
                <tr class="border-b border-border/70" x-show="(!searchTerm || '{{ strtolower($schedule['subject'].' '.$schedule['className'].' '.$schedule['teacherName']) }}'.includes(searchTerm.toLowerCase())) && (filterDay==='all'||filterDay==='{{ $schedule['day'] }}')">
                    <td class="p-4">{{ $schedule['day'] }}</td>
                    <td class="p-4 font-mono text-muted-foreground">{{ $schedule['startTime'] }} – {{ $schedule['endTime'] }}</td>
                    <td class="p-4 font-medium">{{ $schedule['subject'] }}</td>
                    <td class="p-4">{{ $schedule['className'] }}</td>
                    <td class="p-4">{{ $schedule['teacherName'] }}</td>
                    <td class="p-4 text-muted-foreground">{{ $schedule['room'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
