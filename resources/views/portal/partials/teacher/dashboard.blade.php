@php $teacher = $currentTeacher; @endphp
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 text-primary-foreground shadow-lg border border-primary/20">
        <div class="flex items-center gap-4">
            <div class="bg-primary-foreground/20 p-4 rounded-full border border-primary-foreground/30"><x-icon name="graduation-cap" class="w-12 h-12 text-primary-foreground"/></div>
            <div><h2 class="font-display text-primary-foreground mb-1">Selamat Datang, {{ $teacher['name'] ?? 'Guru' }}</h2><p class="text-primary-foreground/80 text-sm">Portal Guru · {{ $teacher['subject'] ?? '' }}</p></div>
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm"><p class="text-sm text-muted-foreground">Email</p><p class="mt-1">{{ $teacher['email'] ?? '-' }}</p></div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm"><p class="text-sm text-muted-foreground">ID Guru</p><p class="mt-1 font-mono">{{ $teacher['id'] ?? '-' }}</p></div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm"><p class="text-sm text-muted-foreground">Terdaftar</p><p class="mt-1">{{ isset($teacher['enrolledDate']) ? \Carbon\Carbon::parse($teacher['enrolledDate'])->locale('id')->isoFormat('D MMM YYYY') : '-' }}</p></div>
    </div>
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h3 class="font-display mb-2">Ringkasan</h3>
        <p class="text-sm text-muted-foreground">Kelola absensi kelas, rekap kehadiran, dan pengajuan cuti melalui tab navigasi di atas.</p>
    </div>
</div>
