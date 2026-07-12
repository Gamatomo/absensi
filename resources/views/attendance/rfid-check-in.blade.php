<x-layouts.kiosk title="RFID Check-in" subtitle="Tempelkan kartu RFID Anda" accent="rfid">
    <x-slot:sidebar>
        <x-live-attendance-list />
    </x-slot:sidebar>

    <div
        class="bg-card border border-border rounded-2xl shadow-xl shadow-primary/5 overflow-hidden"
        x-data="rfidCheckIn()"
        x-init="$nextTick(() => document.getElementById('cardInput')?.focus())"
    >
        {{-- Event badge --}}
        <div class="px-6 py-3 bg-secondary/50 border-b border-border flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-chart-3 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-chart-3"></span>
                </span>
                <span class="text-muted-foreground">Event aktif</span>
            </div>
            <span class="text-sm font-medium truncate max-w-[12rem]" x-text="latestEventName"></span>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Waiting state --}}
            <div x-show="!message && !loading" x-transition class="text-center">
                <div class="relative mx-auto w-40 h-40 mb-6">
                    <div class="absolute inset-0 rounded-full border-2 border-primary/20 kiosk-pulse-ring"></div>
                    <div class="absolute inset-3 rounded-full border border-primary/30 kiosk-pulse-ring" style="animation-delay: 0.5s"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center shadow-lg shadow-primary/25 kiosk-float">
                            <i data-lucide="credit-card" class="w-14 h-14 text-primary-foreground"></i>
                        </div>
                    </div>
                </div>
                <p class="text-lg font-medium font-display">Menunggu Kartu...</p>
                <p class="text-sm text-muted-foreground mt-2">Dekatkan kartu RFID ke reader</p>
                <div class="mt-4 inline-flex items-center gap-2 px-3 py-2 rounded-full border text-sm"
                     :class="wsConnected ? 'bg-primary/5 border-primary/15 text-primary' : 'bg-chart-5/5 border-chart-5/15 text-chart-5'">
                    <i data-lucide="radio" class="w-4 h-4"></i>
                    <span x-text="wsConnected ? 'Reader terhubung dan siap' : 'Menunggu koneksi reader...'"></span>
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                    <svg class="animate-spin w-8 h-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <p class="font-medium">Memverifikasi kartu...</p>
            </div>

            {{-- Result --}}
            <template x-if="message && !loading">
                <div
                    x-transition
                    class="rounded-xl p-6 border text-center"
                    :class="{
                        'bg-chart-3/10 border-chart-3/30': messageType === 'success',
                        'bg-chart-5/10 border-chart-5/30': messageType === 'error',
                        'bg-primary/5 border-primary/20': messageType === 'info'
                    }"
                >
                    <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4"
                         :class="{
                            'bg-chart-3/20': messageType === 'success',
                            'bg-chart-5/20': messageType === 'error',
                            'bg-primary/15': messageType === 'info'
                         }">
                        <template x-if="messageType === 'success'"><i data-lucide="check-circle-2" class="w-9 h-9 text-chart-3"></i></template>
                        <template x-if="messageType === 'error'"><i data-lucide="x-circle" class="w-9 h-9 text-chart-5"></i></template>
                        <template x-if="messageType === 'info'"><i data-lucide="info" class="w-9 h-9 text-primary"></i></template>
                    </div>
                    <p class="text-lg font-display font-medium" x-text="message"></p>
                    <template x-if="userName">
                        <p class="mt-3 text-sm text-muted-foreground">Nama</p>
                        <p class="text-xl font-semibold" x-text="userName"></p>
                    </template>
                    <template x-if="checkInTime">
                        <p class="mt-2 text-sm font-mono text-muted-foreground" x-text="checkInTime"></p>
                    </template>
                </div>
            </template>

            <input id="cardInput" type="text" class="sr-only" @keyup.enter="submitCard" autocomplete="off">

            {{-- Manual fallback --}}
            <div class="mt-6">
                <button
                    type="button"
                    @click="showManualInput = !showManualInput"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm text-muted-foreground hover:text-foreground border border-border rounded-xl hover:bg-secondary/50 transition-colors"
                >
                    <i data-lucide="keyboard" class="w-4 h-4"></i>
                    <span x-text="showManualInput ? 'Sembunyikan input manual' : 'Kartu tidak terbaca? Input manual'"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="showManualInput && 'rotate-180'"></i>
                </button>

                <div x-show="showManualInput" x-transition x-cloak class="mt-4 p-4 rounded-xl bg-secondary/40 border border-border">
                    <label class="text-xs font-medium text-muted-foreground uppercase tracking-wide">ID Kartu Manual</label>
                    <form @submit.prevent="submitManualCard" class="flex gap-2 mt-2">
                        <input
                            x-model="manualCardId"
                            type="text"
                            placeholder="Contoh: CARD-001"
                            class="flex-1 px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/40 font-mono text-sm"
                        >
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-5 py-2.5 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50 font-medium text-sm shadow-sm"
                        >
                            Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-secondary/30 border-t border-border flex items-center gap-3 text-xs text-muted-foreground">
            <i data-lucide="scan-face" class="w-4 h-4 shrink-0"></i>
            <span>Setelah RFID, lanjutkan ke <a href="{{ route('attendance.face') }}" class="text-primary font-medium hover:underline">verifikasi wajah</a> jika diperlukan.</span>
        </div>
    </div>

    @push('scripts')
    <script>
    function rfidCheckIn() {
        return {
            message: '',
            messageType: '',
            userName: '',
            checkInTime: '',
            loading: false,
            showManualInput: false,
            manualCardId: '',
            latestEventName: @json($latestEvent?->name ?? 'Tidak ada event'),
            ws: null,
            wsConnected: false,

            init() {
                this.connectWs();
                this.$nextTick(() => document.getElementById('cardInput')?.focus());
            },

            connectWs() {
                try {
                    // Always connect to localhost (since the scanner is plugged into the kiosk running this browser)
                    const wsUrl = 'ws://127.0.0.1:8765';
                    this.ws = new WebSocket(wsUrl);
                    
                    this.ws.onopen = () => {
                        this.wsConnected = true;
                    };
                    
                    this.ws.onmessage = (event) => {
                        try {
                            const data = JSON.parse(event.data);
                            if (data.uid && !this.loading) {
                                this.verifyCard(data.uid.toUpperCase());
                            }
                        } catch (e) {
                            console.error('Error parsing WS data', e);
                        }
                    };
                    
                    this.ws.onclose = () => {
                        this.wsConnected = false;
                        // Auto reconnect after 3 seconds
                        setTimeout(() => {
                            if (this.$el) this.connectWs();
                        }, 3000);
                    };
                    
                    this.ws.onerror = () => {
                        this.wsConnected = false;
                    };
                } catch (e) {
                    console.error('WebSocket connection failed', e);
                }
            },

            submitCard() {
                const cardId = document.getElementById('cardInput').value.trim();
                if (!cardId) return;
                this.verifyCard(cardId);
                document.getElementById('cardInput').value = '';
            },

            submitManualCard() {
                if (!this.manualCardId.trim()) return;
                this.verifyCard(this.manualCardId);
                this.manualCardId = '';
            },

            verifyCard(cardId) {
                this.loading = true;
                this.message = '';

                fetch(@json(route('attendance.rfid-verify')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ card_id: cardId, device_id: 'kiosk-rfid-01' })
                })
                .then(r => r.json())
                .then(data => {
                    this.message = data.message;
                    this.messageType = data.type;
                    this.userName = data.name || '';
                    this.checkInTime = data.time || '';
                    this.$nextTick(() => window.lucide?.createIcons());

                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('attendance-logged'));
                        setTimeout(() => {
                            window.location.href = '{{ route("attendance.face") }}?profile_key=' + data.profile_key;
                        }, 1500);
                    }
                })
                .catch(e => {
                    this.message = 'Terjadi kesalahan: ' + e.message;
                    this.messageType = 'error';
                    this.$nextTick(() => window.lucide?.createIcons());
                })
                .finally(() => this.loading = false);
            }
        };
    }
    </script>
    @endpush
</x-layouts.kiosk>
