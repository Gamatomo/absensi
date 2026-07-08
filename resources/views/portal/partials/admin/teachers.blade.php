@php $subjects = collect($teachers)->pluck('subject')->unique()->filter()->values(); @endphp
<div class="space-y-6" x-data="teacherAdminData()">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-purple-500/10 rounded-lg"><x-icon name="graduation-cap" class="w-5 h-5 text-purple-600" /></div>
                <div><h2 class="font-display">Data Guru</h2><p class="text-sm text-muted-foreground">{{ count($teachers) }} guru terdaftar</p></div>
            </div>
            <button type="button" @click="showUpload=!showUpload" class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg shadow-sm"><x-icon name="upload" class="w-4 h-4"/>Unggah Data</button>
        </div>

        <div x-show="showUpload" x-cloak class="mb-6 p-6 bg-secondary/30 rounded-lg border border-border">
            <h3 class="mb-4 font-display">Unggah Data Guru</h3>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <label class="flex-1 flex items-center gap-3 px-4 py-3 bg-card border-2 border-dashed border-border hover:border-primary rounded-lg cursor-pointer transition-all">
                    <x-icon name="file-spreadsheet" class="w-5 h-5 text-primary" />
                    <div class="flex-1">
                        <p class="text-sm" x-text="selectedFile || 'Pilih file CSV'"></p>
                        <p class="text-xs text-muted-foreground">Format: CSV dengan header</p>
                    </div>
                    <input type="file" accept=".csv" name="file" class="hidden" @change="selectedFile = $event.target.files[0]?.name" required>
                </label>
                <a href="data:text/csv;charset=utf-8,id,name,email,subject,enrolledDate,phone,address%0ATCH001,Budi Santoso,budi@example.com,Matematika,2026-01-15,081234567890,Jakarta" download="template_data_guru.csv" class="flex items-center gap-2 px-4 py-3 bg-card hover:bg-secondary border border-border rounded-lg transition-all">
                    <x-icon name="download" class="w-4 h-4" /> Template
                </a>
            </div>
            <template x-if="selectedFile">
                <div class="mt-4 p-4 bg-card rounded-lg border border-border">
                    <p class="text-sm mb-2"><x-icon name="check-circle-2" class="w-4 h-4 text-chart-3 inline" /> File siap diunggah: <span x-text="selectedFile"></span></p>
                    <button type="button" @click="submitTeacherUpload" :disabled="uploadStatus === 'uploading'" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg disabled:opacity-50">
                        <span x-show="uploadStatus !== 'uploading'">Unggah Guru</span>
                        <span x-show="uploadStatus === 'uploading'">Mengunggah...</span>
                    </button>
                    <template x-if="uploadMessage">
                        <p :class="uploadStatus === 'success' ? 'text-green-600' : 'text-red-600'" class="text-sm mt-2" x-text="uploadMessage"></p>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" type="text" placeholder="Cari nama, email, atau ID..." class="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-lg focus:ring-2 focus:ring-primary/50"></div>
            <select x-model="filterSubject" class="px-4 py-3 bg-background border border-border rounded-lg"><option value="all">Semua Mapel</option>@foreach($subjects as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($teachers as $teacher)
        <div class="bg-card border border-border hover:border-primary/50 rounded-lg p-6 shadow-sm" x-show="(!searchTerm || '{{ strtolower($teacher['name'].' '.$teacher['email'].' '.$teacher['id']) }}'.includes(searchTerm.toLowerCase())) && (filterSubject==='all'||filterSubject==='{{ $teacher['subject'] }}')">
            <div class="flex justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-display">{{ $teacher['name'] }}</h3>
                        @if($teacher['isActive'] ?? true)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-chart-3/10 text-chart-3 border border-chart-3/30">Aktif</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-chart-5/10 text-chart-5 border border-chart-5/30">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-sm text-muted-foreground font-mono">{{ $teacher['id'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.edit', $teacher['userId']) }}" class="p-2 text-muted-foreground hover:text-primary transition-colors" title="Edit Data & Kelola RFID">
                        <x-icon name="pencil" class="w-5 h-5" />
                    </a>
                    <div class="p-2 bg-purple-500/10 rounded-lg"><x-icon name="graduation-cap" class="w-5 h-5 text-purple-600"/></div>
                </div>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex gap-3"><x-icon name="mail" class="w-4 h-4 text-muted-foreground"/><span class="text-muted-foreground">{{ $teacher['email'] }}</span></div>
                <div class="flex gap-3"><x-icon name="book-open" class="w-4 h-4 text-muted-foreground"/><span>{{ $teacher['subject'] }}</span></div>
            </div>
            <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 {{ !empty($teacher['faceId']) ? 'bg-primary/10 border-primary/20' : 'bg-secondary border-border' }} rounded-md border text-sm font-mono">{{ $teacher['faceId'] ?? 'Belum Ada' }}</div>
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 {{ !empty($teacher['cardId']) ? 'bg-accent/10' : 'bg-secondary' }} rounded-md border border-border text-sm font-mono">{{ $teacher['cardId'] ?? 'Belum Ada' }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function teacherAdminData() {
    return {
        searchTerm: '',
        filterSubject: 'all',
        showUpload: false,
        selectedFile: null,
        uploadStatus: 'idle',
        uploadMessage: '',

        submitTeacherUpload() {
            this.uploadStatus = 'uploading';
            const input = document.querySelector('input[type="file"][name="file"]');
            const formData = new FormData();
            formData.append('file', input.files[0]);

            fetch('{{ route("teachers.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(r => r.json())
            .then(data => {
                this.uploadMessage = data.message;
                if (data.errors && data.errors.length > 0) {
                    this.uploadMessage += " Detail: " + data.errors.slice(0, 3).join(", ");
                    if (data.errors.length > 3) this.uploadMessage += " ...";
                }
                this.uploadStatus = data.success ? 'success' : 'error';
                if (data.success) {
                    setTimeout(() => {
                        this.showUpload = false;
                        this.selectedFile = null;
                        this.uploadMessage = '';
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(e => {
                this.uploadMessage = 'Terjadi kesalahan: ' + e.message;
                this.uploadStatus = 'error';
            });
        }
    };
}
</script>
