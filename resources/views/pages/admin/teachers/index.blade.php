<x-layouts.app title="Data Guru">
    <x-ui.card title="Data Guru" subtitle="Daftar guru dan mata pelajaran.">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border">
                        <th class="pb-3">Nama</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">NIP</th>
                        <th class="pb-3">Mapel</th>
                        <th class="pb-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr class="border-b border-border/70 hover:bg-muted/50 transition-colors">
                            <td class="py-3">{{ $teacher->user->name }}</td>
                            <td class="py-3 text-muted-foreground">{{ $teacher->user->email }}</td>
                            <td class="py-3">{{ $teacher->teacher_number }}</td>
                            <td class="py-3">{{ $teacher->subject }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.users.edit', $teacher->user->id) }}" class="inline-flex items-center justify-center p-2 rounded-md hover:bg-secondary text-muted-foreground hover:text-foreground transition-colors" title="Edit Data & Kelola RFID">
                                    <x-icon name="pencil" class="w-4 h-4"/>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-muted-foreground">Belum ada data guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $teachers->links() }}</div>
    </x-ui.card>
</x-layouts.app>
