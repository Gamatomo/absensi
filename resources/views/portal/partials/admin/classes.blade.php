<div class="space-y-6" x-data="{ searchTerm:'', filterLevel:'all', showForm:false }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3"><div class="p-2.5 bg-primary/10 rounded-lg"><x-icon name="book-open" class="w-5 h-5 text-primary"/></div><div><h2 class="font-display">Data Kelas</h2><p class="text-sm text-muted-foreground">{{ count($classes) }} kelas</p></div></div>
            <button @click="showForm=!showForm" type="button" class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg"><x-icon name="plus" class="w-4 h-4"/>Tambah Kelas</button>
        </div>
        <div x-show="showForm" x-cloak class="mb-6 p-4 bg-secondary/30 rounded-lg border border-border grid md:grid-cols-2 gap-3">
            <input placeholder="Nama kelas" class="px-3 py-2 border border-border rounded-lg bg-background">
            <input placeholder="Ruang" class="px-3 py-2 border border-border rounded-lg bg-background">
            <button type="button" class="md:col-span-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg">Simpan (Preview UI)</button>
        </div>
        <div class="flex gap-4"><div class="flex-1 relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari kelas..."></div>
        <select x-model="filterLevel" class="px-4 py-3 border border-border rounded-lg bg-background"><option value="all">Semua Tingkat</option><option>X</option><option>XI</option><option>XII</option></select></div>
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
