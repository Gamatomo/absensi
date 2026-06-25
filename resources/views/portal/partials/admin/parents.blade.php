<div class="space-y-6" x-data="{ searchTerm:'' }">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6"><div class="p-2.5 bg-rose-500/10 rounded-lg"><x-icon name="heart" class="w-5 h-5 text-rose-600"/></div><div><h2 class="font-display">Data Orang Tua</h2><p class="text-sm text-muted-foreground">{{ count($parents) }} orang tua terdaftar</p></div></div>
        <div class="relative"><x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/><input x-model="searchTerm" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari nama, email, atau siswa..."></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($parents as $parent)
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm" x-show="!searchTerm || '{{ strtolower($parent['name'].' '.$parent['studentName'].' '.$parent['email']) }}'.includes(searchTerm.toLowerCase())">
            <div class="flex items-start justify-between mb-1">
                <h3 class="font-display">{{ $parent['name'] }}</h3>
                @if($parent['isActive'] ?? true)
                <span class="text-xs px-2 py-0.5 rounded-full bg-chart-3/10 text-chart-3 border border-chart-3/30">Aktif</span>
                @else
                <span class="text-xs px-2 py-0.5 rounded-full bg-chart-5/10 text-chart-5 border border-chart-5/30">Nonaktif</span>
                @endif
            </div>
            <p class="text-sm text-muted-foreground mb-4">{{ $parent['relationship'] }} dari {{ $parent['studentName'] }}</p>
            <div class="space-y-2 text-sm">
                <div class="flex gap-2"><x-icon name="phone" class="w-4 h-4 text-muted-foreground"/>{{ $parent['phone'] }}</div>
                <div class="flex gap-2"><x-icon name="mail" class="w-4 h-4 text-muted-foreground"/>{{ $parent['email'] }}</div>
                <div class="flex gap-2"><x-icon name="briefcase" class="w-4 h-4 text-muted-foreground"/>{{ $parent['occupation'] }}</div>
                <div class="flex gap-2"><x-icon name="map-pin" class="w-4 h-4 text-muted-foreground"/>{{ $parent['address'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
