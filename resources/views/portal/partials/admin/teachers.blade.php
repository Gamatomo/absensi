@php $subjects = collect($teachers)->pluck('subject')->unique()->filter()->values(); @endphp
<div class="space-y-6" x-data="{ searchTerm: '', filterSubject: 'all', showUpload: false }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-purple-500/10 rounded-lg"><x-icon name="graduation-cap" class="w-5 h-5 text-purple-600" /></div>
                <div><h2 class="font-display">Data Guru</h2><p class="text-sm text-muted-foreground">{{ count($teachers) }} guru terdaftar</p></div>
            </div>
            <button type="button" @click="showUpload=!showUpload" class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg shadow-sm"><x-icon name="upload" class="w-4 h-4"/>Unggah Data</button>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" type="text" placeholder="Cari nama, email, atau ID..." class="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-lg focus:ring-2 focus:ring-primary/50"></div>
            <select x-model="filterSubject" class="px-4 py-3 bg-background border border-border rounded-lg"><option value="all">Semua Mapel</option>@foreach($subjects as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($teachers as $teacher)
        <div class="bg-card border border-border hover:border-primary/50 rounded-lg p-6 shadow-sm" x-show="(!searchTerm || '{{ strtolower($teacher['name'].' '.$teacher['email'].' '.$teacher['id']) }}'.includes(searchTerm.toLowerCase())) && (filterSubject==='all'||filterSubject==='{{ $teacher['subject'] }}')">
            <div class="flex justify-between mb-4"><div><h3 class="font-display">{{ $teacher['name'] }}</h3><p class="text-sm text-muted-foreground font-mono">{{ $teacher['id'] }}</p></div><div class="p-2 bg-purple-500/10 rounded-lg"><x-icon name="graduation-cap" class="w-5 h-5 text-purple-600"/></div></div>
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
