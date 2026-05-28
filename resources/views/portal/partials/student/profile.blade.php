@php $student = $currentStudent; @endphp
<div class="bg-card border border-border rounded-lg p-6 shadow-sm max-w-3xl">
    <h2 class="font-display mb-6 flex items-center gap-2"><x-icon name="user" class="w-5 h-5 text-primary"/>Profil Siswa</h2>
    <form class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="text-sm text-muted-foreground">Nama</label><input value="{{ $student['name'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Email</label><input value="{{ $student['email'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">NISN</label><input value="{{ $student['nisn'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Telepon</label><input value="{{ $student['phone'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div class="md:col-span-2"><label class="text-sm text-muted-foreground">Alamat</label><textarea class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" rows="2">{{ $student['address'] ?? '' }}</textarea></div>
        </div>
        <button type="button" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg">Simpan Perubahan</button>
    </form>
</div>
