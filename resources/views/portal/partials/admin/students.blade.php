@php
    $departments = collect($students)->pluck('department')->unique()->filter()->values();
@endphp

<div class="space-y-6" x-data="{ searchTerm: '', filterDepartment: 'all', showUpload: false, selectedFile: null, parsedCount: 0, uploadStatus: 'idle' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-primary/10 rounded-lg"><x-icon name="users" class="w-5 h-5 text-primary" /></div>
                <div>
                    <h2 class="font-display">Data Siswa</h2>
                    <p class="text-sm text-muted-foreground">{{ count($students) }} siswa terdaftar</p>
                </div>
            </div>
            <button type="button" @click="showUpload = !showUpload" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm">
                <x-icon name="upload" class="w-4 h-4" /> Unggah Data
            </button>
        </div>

        <div x-show="showUpload" x-cloak class="mb-6 p-6 bg-secondary/30 rounded-lg border border-border">
            <h3 class="mb-4 font-display">Unggah Data Siswa</h3>
            <div class="flex items-center gap-4">
                <label class="flex-1 flex items-center gap-3 px-4 py-3 bg-card border-2 border-dashed border-border hover:border-primary rounded-lg cursor-pointer transition-all">
                    <x-icon name="file-spreadsheet" class="w-5 h-5 text-primary" />
                    <div class="flex-1">
                        <p class="text-sm" x-text="selectedFile || 'Pilih file CSV'"></p>
                        <p class="text-xs text-muted-foreground">Format: CSV dengan header</p>
                    </div>
                    <input type="file" accept=".csv" class="hidden" @change="selectedFile = $event.target.files[0]?.name; parsedCount = 1">
                </label>
                <a href="data:text/csv;charset=utf-8,id,name,email,nisn,cardId,faceId,department,enrolledDate,phone,address%0ASTU001,Ahmad Rizki,ahmad@example.com,0012345678,CARD123,FACE456,Teknik Informatika,2026-01-15,081234567890,Jakarta" download="template_data_siswa.csv" class="flex items-center gap-2 px-4 py-3 bg-card hover:bg-secondary border border-border rounded-lg transition-all">
                    <x-icon name="download" class="w-4 h-4" /> Template
                </a>
            </div>
            <template x-if="parsedCount > 0">
                <div class="mt-4 p-4 bg-card rounded-lg border border-border">
                    <p class="text-sm mb-2"><x-icon name="check-circle-2" class="w-4 h-4 text-chart-3 inline" /> Berhasil memproses data siswa (preview)</p>
                    <button type="button" @click="uploadStatus='success'; setTimeout(() => { showUpload=false; uploadStatus='idle' }, 1500)" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg">Unggah Siswa</button>
                </div>
            </template>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
                <input type="text" x-model="searchTerm" placeholder="Cari berdasarkan nama, email, atau ID..." class="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <select x-model="filterDepartment" class="px-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 cursor-pointer">
                <option value="all">Semua Jurusan</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($students as $student)
        <div
            class="bg-card border border-border hover:border-primary/50 rounded-lg p-6 shadow-sm hover:shadow-md transition-all"
            x-show="(!searchTerm || '{{ strtolower($student['name'].' '.$student['email'].' '.$student['id']) }}'.includes(searchTerm.toLowerCase())) && (filterDepartment === 'all' || filterDepartment === '{{ $student['department'] }}')"
        >
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="mb-1 font-display">{{ $student['name'] }}</h3>
                    <p class="text-sm text-muted-foreground font-mono">{{ $student['id'] }}</p>
                </div>
                <div class="p-2 bg-primary/10 rounded-lg"><x-icon name="users" class="w-5 h-5 text-primary" /></div>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-3"><x-icon name="mail" class="w-4 h-4 text-muted-foreground" /><span class="text-muted-foreground">{{ $student['email'] }}</span></div>
                <div class="flex items-center gap-3"><x-icon name="building-2" class="w-4 h-4 text-muted-foreground" /><span>{{ $student['department'] }}</span></div>
                <div class="flex items-center gap-3"><x-icon name="calendar" class="w-4 h-4 text-muted-foreground" /><span class="text-muted-foreground">Terdaftar: {{ \Carbon\Carbon::parse($student['enrolledDate'])->locale('id')->isoFormat('D MMM YYYY') }}</span></div>
            </div>
            <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                @if(!empty($student['faceId']))
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-primary/10 rounded-md border border-primary/20"><x-icon name="scan-face" class="w-4 h-4 text-primary" /><span class="text-sm text-primary font-mono">{{ $student['faceId'] }}</span></div>
                @else
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-secondary rounded-md border border-border"><x-icon name="scan-face" class="w-4 h-4 text-muted-foreground" /><span class="text-sm text-muted-foreground">Belum Ada</span></div>
                @endif
                @if(!empty($student['cardId']))
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-accent/10 rounded-md border border-accent/20"><x-icon name="credit-card" class="w-4 h-4 text-accent" /><span class="text-sm text-accent font-mono">{{ $student['cardId'] }}</span></div>
                @else
                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-secondary rounded-md border border-border"><x-icon name="credit-card" class="w-4 h-4 text-muted-foreground" /><span class="text-sm text-muted-foreground">Belum Ada</span></div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
