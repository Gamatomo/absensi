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
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md" x-show="(!searchTerm || '{{ strtolower($class['name'].' '.$class['department']) }}'.includes(searchTerm.toLowerCase())) && (filterLevel==='all'||filterLevel==='{{ $class['level'] }}')">
            <div class="flex justify-between mb-3"><h3 class="font-display text-lg">{{ $class['name'] }}</h3><span class="text-xs px-2 py-1 bg-secondary rounded border border-border">{{ $class['level'] }}</span></div>
            <p class="text-sm text-muted-foreground mb-2">{{ $class['department'] }}</p>
            <div class="space-y-2 text-sm"><div class="flex gap-2"><x-icon name="graduation-cap" class="w-4 h-4"/>{{ $class['homeroomTeacherName'] ?: '-' }}</div><div class="flex gap-2"><x-icon name="users" class="w-4 h-4"/>{{ $class['studentCount'] }} siswa</div><div class="flex gap-2"><x-icon name="map-pin" class="w-4 h-4"/>{{ $class['room'] }}</div></div>
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
