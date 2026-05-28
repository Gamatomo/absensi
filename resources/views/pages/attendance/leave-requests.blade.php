<x-layouts.app title="Leave Requests">
    <x-ui.card title="Pengajuan Izin/Cuti" subtitle="Approval request untuk siswa dan guru.">
        <div class="space-y-3">
            @forelse($requests as $request)
                <div class="border border-border rounded-lg p-4 bg-secondary/30"><div class="flex items-center justify-between gap-3"><p class="font-medium">{{ $request->user->name ?? '-' }} - {{ $request->reason }}</p><span class="text-xs uppercase px-2 py-1 rounded bg-card border border-border">{{ $request->status }}</span></div><p class="text-sm text-muted-foreground mt-2">{{ $request->start_date->format('Y-m-d') }} s.d. {{ $request->end_date->format('Y-m-d') }}</p></div>
            @empty
                <p class="text-sm text-muted-foreground">Belum ada pengajuan izin.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    </x-ui.card>
</x-layouts.app>
