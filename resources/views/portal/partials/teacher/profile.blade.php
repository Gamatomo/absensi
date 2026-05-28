@php $teacher = $currentTeacher; @endphp
<div class="bg-card border border-border rounded-lg p-6 shadow-sm max-w-3xl">
    <h2 class="font-display mb-6 flex items-center gap-2"><x-icon name="user" class="w-5 h-5 text-primary"/>Profil Guru</h2>
    <form class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="text-sm text-muted-foreground">Nama</label><input value="{{ $teacher['name'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Email</label><input value="{{ $teacher['email'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Mata Pelajaran</label><input value="{{ $teacher['subject'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            <div><label class="text-sm text-muted-foreground">Telepon</label><input value="{{ $teacher['phone'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
        </div>
        <button type="button" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg">Simpan Perubahan</button>
    </form>
</div>
