<x-layouts.app title="Data Siswa">
    <x-ui.card title="Data Siswa" subtitle="Daftar siswa terdaftar beserta nomor induk.">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border"><th class="pb-3">Nama</th><th class="pb-3">Email</th><th class="pb-3">NIS</th><th class="pb-3">Jurusan</th></tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="border-b border-border/70"><td class="py-3">{{ $student->user->name }}</td><td class="py-3 text-muted-foreground">{{ $student->user->email }}</td><td class="py-3">{{ $student->student_number }}</td><td class="py-3">{{ $student->department }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-muted-foreground">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $students->links() }}</div>
    </x-ui.card>
</x-layouts.app>
