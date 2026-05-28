@php
    $teacher = $currentTeacher;
    $myRequests = collect($leaveRequests)->where('studentId', $teacher['id'] ?? '');
@endphp
<div class="space-y-6">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-4 flex items-center gap-2"><x-icon name="file-text" class="w-5 h-5 text-primary"/>Pengajuan Cuti</h2>
        <form class="grid md:grid-cols-2 gap-4">
            <div><label class="text-sm text-muted-foreground">Alasan</label><input class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Sakit / Cuti tahunan"></div>
            <div><label class="text-sm text-muted-foreground">Tanggal Mulai</label><input type="date" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Tanggal Selesai</label><input type="date" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div class="md:col-span-2"><label class="text-sm text-muted-foreground">Keterangan</label><textarea rows="3" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></textarea></div>
            <div class="md:col-span-2"><button type="button" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg">Kirim Pengajuan Cuti</button></div>
        </form>
    </div>
    <div class="space-y-3">
        @forelse($myRequests as $request)
        <div class="bg-card border border-border rounded-lg p-4">
            <div class="flex justify-between"><p class="font-medium">{{ $request['reason'] }}</p><span class="text-xs uppercase px-2 py-1 rounded border">{{ $request['status'] }}</span></div>
            <p class="text-sm text-muted-foreground mt-1">{{ $request['startDate'] }} – {{ $request['endDate'] }}</p>
        </div>
        @empty
        <p class="text-sm text-muted-foreground">Belum ada pengajuan cuti.</p>
        @endforelse
    </div>
</div>
