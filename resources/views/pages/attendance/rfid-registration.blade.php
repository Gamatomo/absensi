<x-layouts.app title="RFID Registration">
    <x-ui.card title="RFID Registration" subtitle="Mapping kartu RFID ke user account.">
        <div class="space-y-3">
            @forelse($cards as $card)
                <div class="border border-border rounded-lg p-4 flex justify-between items-center bg-secondary/30"><div><p class="font-medium">{{ $card->uid }}</p><p class="text-sm text-muted-foreground">{{ $card->user->name ?? '-' }}</p></div><span class="text-xs uppercase px-2 py-1 rounded bg-card border border-border">{{ $card->status }}</span></div>
            @empty
                <p class="text-sm text-muted-foreground">Belum ada kartu RFID terdaftar.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $cards->links() }}</div>
    </x-ui.card>
</x-layouts.app>
