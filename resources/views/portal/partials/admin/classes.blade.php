@php
    $classStudentMap = [];
    foreach ($classes as $class) {
        $classStudentMap[$class['id']] = collect($students)->filter(fn($s) => ($s['classId'] ?? null) == $class['id'])->values()->all();
    }
    $unassignedStudents = collect($students)->filter(fn($s) => empty($s['classId']))->values()->all();
@endphp
<div class="space-y-6" x-data="classAdminData()">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3"><div class="p-2.5 bg-primary/10 rounded-lg"><x-icon name="book-open" class="w-5 h-5 text-primary"/></div><div><h2 class="font-display">Data Kelas</h2><p class="text-sm text-muted-foreground">{{ count($classes) }} kelas</p></div></div>
            <button @click="showForm=!showForm" type="button" class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg"><x-icon name="plus" class="w-4 h-4"/>Tambah Kelas</button>
        </div>

        <div x-show="showForm" x-cloak class="mb-6 p-4 bg-secondary/30 rounded-lg border border-border">
            <h3 class="font-display mb-4">Form Tambah Kelas</h3>
            <template x-if="message">
                <div :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" class="mb-4 p-3 border rounded-lg text-sm" x-text="message"></div>
            </template>
            <form @submit.prevent="submitClass($event)" class="grid md:grid-cols-2 gap-3">
                @csrf
                <div>
                    <label class="text-sm text-muted-foreground">Nama Kelas</label>
                    <input name="name" placeholder="Contoh: X-A" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                </div>
                <div>
                    <label class="text-sm text-muted-foreground">Tingkat</label>
                    <select name="level" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                        <option value="">Pilih Tingkat</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-muted-foreground">Jurusan</label>
                    <input name="department" placeholder="Contoh: Teknik Informatika" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                </div>
                <div>
                    <label class="text-sm text-muted-foreground">Ruang</label>
                    <input name="room" placeholder="Contoh: A101" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                </div>
                <div>
                    <label class="text-sm text-muted-foreground">Tahun Ajaran</label>
                    <input name="academic_year" placeholder="Contoh: 2025/2026" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                </div>
                <div>
                    <label class="text-sm text-muted-foreground">Guru Piket (Opsional)</label>
                    <select name="homeroom_teacher_id" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
                        <option value="">- Pilih Guru -</option>
                        @foreach($teachers as $teacher)
                        <option value="">{{ $teacher['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" :disabled="loading" class="md:col-span-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg disabled:opacity-50">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </form>
        </div>

        <div class="flex gap-4"><div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari kelas..."></div>
        <select x-model="filterLevel" class="px-4 py-3 border border-border rounded-lg bg-background"><option value="all">Semua Tingkat</option><option value="X">X</option><option value="XI">XI</option><option value="XII">XII</option></select></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($classes as $class)
        @php $classStudents = $classStudentMap[$class['id']] ?? []; @endphp
        <div class="bg-card border border-border rounded-lg shadow-sm hover:shadow-md transition-all" x-show="(!searchTerm || '{{ strtolower($class['name'].' '.$class['department']) }}'.includes(searchTerm.toLowerCase())) && (filterLevel==='all'||filterLevel==='{{ $class['level'] }}')" x-data="{ expanded: false, showAddStudent: false, addMessage: '', addMessageType: '', addLoading: false, searchStudent: '', selectedStudents: [] }">
            <div class="p-6">
                <div class="flex justify-between mb-3"><h3 class="font-display text-lg">{{ $class['name'] }}</h3><span class="text-xs px-2 py-1 bg-secondary rounded border border-border">{{ $class['level'] }}</span></div>
                <p class="text-sm text-muted-foreground mb-2">{{ $class['department'] }}</p>
                <div class="space-y-2 text-sm">
                    <div class="flex gap-2"><x-icon name="graduation-cap" class="w-4 h-4"/>{{ $class['homeroomTeacherName'] ?: '-' }}</div>
                    <div class="flex gap-2"><x-icon name="users" class="w-4 h-4"/>{{ count($classStudents) }} siswa</div>
                    <div class="flex gap-2"><x-icon name="map-pin" class="w-4 h-4"/>{{ $class['room'] }}</div>
                </div>
                <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                    <button @click="expanded = !expanded; $nextTick(() => window.lucide && window.lucide.createIcons())" type="button" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm rounded-lg border border-border hover:bg-secondary transition-all">
                        <x-icon name="users" class="w-4 h-4"/>
                        <span x-text="expanded ? 'Sembunyikan' : 'Kelola Siswa'"></span>
                    </button>
                </div>
            </div>

            {{-- Student management panel --}}
            <div x-show="expanded" x-cloak class="border-t border-border p-4 bg-secondary/20">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium">Daftar Siswa di Kelas Ini</h4>
                    <button @click="showAddStudent = !showAddStudent; $nextTick(() => window.lucide && window.lucide.createIcons())" type="button" class="text-xs px-3 py-1.5 bg-primary text-primary-foreground rounded-lg flex items-center gap-1">
                        <x-icon name="plus" class="w-3 h-3"/> Tambah
                    </button>
                </div>

                <template x-if="addMessage">
                    <div :class="addMessageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" class="mb-3 p-2 border rounded-lg text-xs" x-text="addMessage"></div>
                </template>

                {{-- Add student form --}}
                <div x-show="showAddStudent" x-cloak class="mb-3 p-3 bg-background rounded-lg border border-border">
                    <label class="text-xs font-medium mb-2 block">Pilih Siswa (belum punya kelas)</label>
                    
                    <div class="relative mb-2">
                        <x-icon name="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"/>
                        <input type="text" x-model="searchStudent" placeholder="Cari nama atau ID..." class="w-full pl-8 pr-3 py-1.5 border border-border rounded-lg bg-background text-sm">
                    </div>

                    <div class="max-h-40 overflow-y-auto border border-border rounded-lg bg-background mb-3 space-y-1 p-1">
                        @forelse($unassignedStudents as $us)
                        <label x-show="!searchStudent || '{{ strtolower($us['name'].' '.$us['id']) }}'.includes(searchStudent.toLowerCase())" class="flex items-center gap-2 p-2 hover:bg-secondary rounded cursor-pointer transition-colors">
                            <input type="checkbox" value="{{ $us['dbId'] }}" x-model="selectedStudents" class="rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm">
                                <span class="font-medium">{{ $us['name'] }}</span> 
                                <span class="text-muted-foreground text-xs">({{ $us['id'] }})</span>
                            </span>
                        </label>
                        @empty
                        <div class="p-2 text-center text-xs text-muted-foreground">Tidak ada siswa yang belum mendapatkan kelas</div>
                        @endforelse
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-muted-foreground"><span x-text="selectedStudents.length"></span> siswa dipilih</span>
                        <button @click="
                            addLoading = true; addMessage = '';
                            if (selectedStudents.length === 0) { addMessage = 'Pilih minimal 1 siswa'; addMessageType = 'error'; addLoading = false; return; }
                            fetch('/classes/{{ $class['id'] }}/students', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'X-Requested-With': 'XMLHttpRequest' },
                                body: JSON.stringify({ student_ids: selectedStudents.map(Number) })
                            }).then(r => r.json()).then(d => { addMessage = d.message; addMessageType = d.success ? 'success' : 'error'; if(d.success) setTimeout(() => window.location.reload(), 1200); }).catch(e => { addMessage = 'Error: ' + e.message; addMessageType = 'error'; }).finally(() => addLoading = false);
                        " :disabled="addLoading || selectedStudents.length === 0" type="button" class="px-3 py-1.5 bg-primary text-primary-foreground rounded-lg text-sm disabled:opacity-50">
                            <span x-show="!addLoading">Tambah ke Kelas</span>
                            <span x-show="addLoading">Menyimpan...</span>
                        </button>
                    </div>
                </div>

                {{-- Current students list --}}
                @if(count($classStudents) > 0)
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach($classStudents as $cs)
                    <div class="flex items-center justify-between p-2 bg-background rounded-lg border border-border text-sm">
                        <div>
                            <span class="font-medium">{{ $cs['name'] }}</span>
                            <span class="text-xs text-muted-foreground ml-2">{{ $cs['id'] }}</span>
                        </div>
                        <button @click="
                            if(!confirm('Hapus {{ $cs['name'] }} dari kelas ini?')) return;
                            fetch('/classes/{{ $class['id'] }}/students/{{ $cs['dbId'] }}', {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(r => r.json()).then(d => { addMessage = d.message; addMessageType = d.success ? 'success' : 'error'; if(d.success) setTimeout(() => window.location.reload(), 1000); }).catch(e => { addMessage = 'Error: ' + e.message; addMessageType = 'error'; });
                        " type="button" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-all">
                            <x-icon name="x" class="w-4 h-4"/>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-muted-foreground text-center py-3">Belum ada siswa di kelas ini.</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function classAdminData() {
    return {
        searchTerm: '',
        filterLevel: 'all',
        showForm: false,
        loading: false,
        message: '',
        messageType: '',

        submitClass(e) {
            this.loading = true;
            const formData = new FormData(e.target);

            fetch('{{ route("classes.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(r => r.json())
            .then(data => {
                this.message = data.message;
                this.messageType = data.success ? 'success' : 'error';
                if (data.success) {
                    e.target.reset();
                    setTimeout(() => {
                        this.showForm = false;
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(e => {
                this.message = 'Terjadi kesalahan: ' + e.message;
                this.messageType = 'error';
            })
            .finally(() => this.loading = false);
        }
    };
}
</script>
