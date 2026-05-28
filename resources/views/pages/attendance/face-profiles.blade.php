<x-layouts.app title="Face Profiles">
    <x-ui.card title="Face Verification Profiles" subtitle="Profil wajah terdaftar untuk verifikasi absensi.">
        <div class="space-y-3">
            @forelse($profiles as $profile)
                <div class="border border-border rounded-lg p-4 bg-secondary/30"><p class="font-medium">{{ $profile->user->name ?? '-' }}</p><p class="text-sm text-muted-foreground">Key: {{ $profile->profile_key }}</p><p class="text-xs text-muted-foreground mt-1">Samples: {{ $profile->samples_count }} | Active: {{ $profile->is_active ? 'Yes' : 'No' }}</p></div>
            @empty
                <p class="text-sm text-muted-foreground">Belum ada face profile.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $profiles->links() }}</div>
    </x-ui.card>
</x-layouts.app>
