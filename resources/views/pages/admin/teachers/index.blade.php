<x-layouts.app title="Data Guru">
    <x-ui.card title="Data Guru" subtitle="Daftar guru dan mata pelajaran.">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border"><th class="pb-3">Nama</th><th class="pb-3">Email</th><th class="pb-3">NIP</th><th class="pb-3">Mapel</th></tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr class="border-b border-border/70"><td class="py-3">{{ $teacher->user->name }}</td><td class="py-3 text-muted-foreground">{{ $teacher->user->email }}</td><td class="py-3">{{ $teacher->teacher_number }}</td><td class="py-3">{{ $teacher->subject }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-muted-foreground">Belum ada data guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $teachers->links() }}</div>
    </x-ui.card>
</x-layouts.app>
