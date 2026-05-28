@php
    $studentIds = collect($students)->pluck('id')->all();
@endphp
<div class="space-y-6" x-data="{ activeView:'students', searchQuery:'', filterStatus:'all' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3"><div class="p-2.5 bg-orange-500/10 rounded-lg"><x-icon name="file-text" class="w-5 h-5 text-orange-600"/></div><div><h2 class="font-display">Kelola Izin/Cuti</h2><p class="text-sm text-muted-foreground">{{ count($leaveRequests) }} pengajuan</p></div></div>
            <div class="flex gap-2">
                <button @click="activeView='students'" :class="activeView==='students'?'bg-primary text-primary-foreground':'bg-secondary'" class="px-4 py-2 rounded-lg text-sm">Siswa</button>
                <button @click="activeView='teachers'" :class="activeView==='teachers'?'bg-primary text-primary-foreground':'bg-secondary'" class="px-4 py-2 rounded-lg text-sm">Guru</button>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchQuery" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari nama atau alasan..."></div>
            <select x-model="filterStatus" class="px-4 py-3 border border-border rounded-lg bg-background"><option value="all">Semua Status</option><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select>
        </div>
    </div>
    <div class="space-y-4">
        @foreach($leaveRequests as $request)
            @php
                $person = collect($students)->firstWhere('id', $request['studentId']) ?? collect($teachers)->firstWhere('id', $request['studentId']);
                $isStudent = in_array($request['studentId'], $studentIds, true);
            @endphp
            <div class="bg-card border border-border rounded-lg p-6 shadow-sm"
                 x-show="(activeView==='students' ? {{ $isStudent ? 'true' : 'false' }} : {{ $isStudent ? 'false' : 'true' }}) && (filterStatus==='all'||filterStatus==='{{ $request['status'] }}') && (!searchQuery || '{{ strtolower(($person['name'] ?? '').' '.$request['reason']) }}'.includes(searchQuery.toLowerCase()))">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div>
                        <h3 class="font-display">{{ $person['name'] ?? 'Unknown' }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $request['reason'] }} · {{ \Carbon\Carbon::parse($request['startDate'])->locale('id')->isoFormat('D MMM YYYY') }} – {{ \Carbon\Carbon::parse($request['endDate'])->locale('id')->isoFormat('D MMM YYYY') }}</p>
                        <p class="text-sm mt-2">{{ $request['description'] }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs uppercase px-3 py-1 rounded-full border border-border {{ $request['status']==='approved'?'bg-chart-3/10 text-chart-3':($request['status']==='rejected'?'bg-chart-5/10 text-chart-5':'bg-chart-4/10 text-chart-4') }}">{{ $request['status'] }}</span>
                        @if($request['status']==='pending')
                        <button type="button" class="px-3 py-1.5 bg-chart-3/10 text-chart-3 rounded-lg text-sm border border-chart-3/30">Setujui</button>
                        <button type="button" class="px-3 py-1.5 bg-chart-5/10 text-chart-5 rounded-lg text-sm border border-chart-5/30">Tolak</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
