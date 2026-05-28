<x-layouts.app title="Attendance Logs">
    <x-ui.card title="Attendance Logs" subtitle="Hasil normalisasi dari event RFID + face verification.">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border"><th class="pb-3">Tanggal</th><th class="pb-3">Nama</th><th class="pb-3">Status</th><th class="pb-3">Masuk</th></tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr class="border-b border-border/70"><td class="py-3">{{ $record->attendance_date?->format('Y-m-d') }}</td><td class="py-3">{{ $record->user->name ?? '-' }}</td><td class="py-3 capitalize">{{ $record->status }}</td><td class="py-3">{{ $record->check_in_time }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-muted-foreground">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </x-ui.card>
</x-layouts.app>
