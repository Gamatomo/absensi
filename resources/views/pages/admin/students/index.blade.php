<x-layouts.app title="Data Siswa">
    <x-ui.card title="Data Siswa" subtitle="Daftar siswa terdaftar beserta nomor induk.">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border">
                        <th class="pb-3">Nama</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">NIS</th>
                        <th class="pb-3">Jurusan</th>
                        <th class="pb-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="border-b border-border/70 hover:bg-muted/50 transition-colors">
                            <td class="py-3">{{ $student->user->name }}</td>
                            <td class="py-3 text-muted-foreground">{{ $student->user->email }}</td>
                            <td class="py-3">{{ $student->student_number }}</td>
                            <td class="py-3">{{ $student->department }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.users.edit', $student->user->id) }}" class="inline-flex items-center justify-center p-2 rounded-md hover:bg-secondary text-muted-foreground hover:text-foreground transition-colors" title="Edit Data & Kelola RFID">
                                    <x-icon name="pencil" class="w-4 h-4"/>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-muted-foreground">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $students->links() }}</div>
    </x-ui.card>
</x-layouts.app>
