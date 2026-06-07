<x-layouts.kiosk title="Face Recognition" subtitle="Verifikasi wajah Anda" accent="face">
    <div
        class="bg-card border border-border rounded-2xl shadow-xl shadow-emerald-500/5 overflow-hidden"
        x-data="faceRecognition()"
        x-init="$nextTick(() => document.getElementById('faceInput')?.focus())"
    >
        <div class="px-6 py-3 bg-secondary/50 border-b border-border flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm">
                <i data-lucide="scan-face" class="w-4 h-4 text-emerald-600"></i>
                <span class="text-muted-foreground">Pengenalan Wajah AI</span>
            </div>
            <span class="text-sm font-medium truncate max-w-[12rem]" x-text="latestEventName"></span>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Camera viewport --}}
            <div x-show="!message && !loading" x-transition>
                <div class="relative rounded-2xl overflow-hidden aspect-[4/3] bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 border border-emerald-500/20 shadow-inner mb-5">
                    {{-- Corner brackets --}}
                    <div class="absolute top-4 left-4 w-10 h-10 border-t-2 border-l-2 border-emerald-400/80 rounded-tl-lg"></div>
                    <div class="absolute top-4 right-4 w-10 h-10 border-t-2 border-r-2 border-emerald-400/80 rounded-tr-lg"></div>
                    <div class="absolute bottom-4 left-4 w-10 h-10 border-b-2 border-l-2 border-emerald-400/80 rounded-bl-lg"></div>
                    <div class="absolute bottom-4 right-4 w-10 h-10 border-b-2 border-r-2 border-emerald-400/80 rounded-br-lg"></div>

                    {{-- Scan line --}}
                    <div class="absolute left-6 right-6 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent kiosk-scan-line shadow-[0_0_12px_rgba(52,211,153,0.6)]"></div>

                    {{-- Center content --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                        <div class="relative mb-4">
                            <div class="absolute inset-0 rounded-full bg-emerald-400/20 blur-xl scale-150"></div>
                            <div class="relative w-20 h-20 rounded-full bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center">
                                <i data-lucide="user-round" class="w-10 h-10 text-emerald-300"></i>
                            </div>
                        </div>
                        <p class="text-emerald-100 font-medium text-sm sm:text-base">Posisikan wajah di dalam bingkai</p>
                        <p class="text-emerald-400/70 text-xs mt-1">Pastikan pencahayaan cukup dan wajah menghadap kamera</p>
                    </div>

                    {{-- Grid overlay --}}
                    <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size: 24px 24px;"></div>
                </div>

                <div class="flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <span class="w-2 h-2 rounded-full bg-chart-3 animate-pulse"></span>
                        Kamera aktif
                    </div>
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        Verifikasi aman
                    </div>
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="text-center py-10">
                <div class="relative mx-auto w-24 h-24 mb-5">
                    <div class="absolute inset-0 rounded-full border-2 border-emerald-500/30 animate-spin" style="border-top-color: rgb(16 185 129)"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i data-lucide="scan-face" class="w-10 h-10 text-emerald-600"></i>
                    </div>
                </div>
                <p class="font-display font-medium">Menganalisis wajah...</p>
                <p class="text-sm text-muted-foreground mt-1">Mohon tetap diam sebentar</p>
            </div>

            {{-- Result --}}
            <template x-if="message && !loading">
                <div
                    x-transition
                    class="rounded-xl p-6 border text-center"
                    :class="{
                        'bg-chart-3/10 border-chart-3/30': messageType === 'success',
                        'bg-chart-5/10 border-chart-5/30': messageType === 'error',
                        'bg-emerald-500/5 border-emerald-500/20': messageType === 'info'
                    }"
                >
                    <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4"
                         :class="{
                            'bg-chart-3/20': messageType === 'success',
                            'bg-chart-5/20': messageType === 'error',
                            'bg-emerald-500/15': messageType === 'info'
                         }">
                        <template x-if="messageType === 'success'"><i data-lucide="check-circle-2" class="w-9 h-9 text-chart-3"></i></template>
                        <template x-if="messageType === 'error'"><i data-lucide="x-circle" class="w-9 h-9 text-chart-5"></i></template>
                        <template x-if="messageType === 'info'"><i data-lucide="info" class="w-9 h-9 text-emerald-600"></i></template>
                    </div>
                    <p class="text-lg font-display font-medium" x-text="message"></p>
                    <template x-if="userName">
                        <p class="mt-3 text-sm text-muted-foreground">Identitas terverifikasi</p>
                        <p class="text-xl font-semibold" x-text="userName"></p>
                    </template>
                    <template x-if="checkInTime">
                        <p class="mt-2 text-sm font-mono text-muted-foreground" x-text="checkInTime"></p>
                    </template>
                </div>
            </template>

            <input id="faceInput" type="text" class="sr-only" @keyup.enter="submitFace" autocomplete="off">

            <div class="mt-6">
                <button
                    type="button"
                    @click="showManualInput = !showManualInput"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm text-muted-foreground hover:text-foreground border border-border rounded-xl hover:bg-secondary/50 transition-colors"
                >
                    <i data-lucide="keyboard" class="w-4 h-4"></i>
                    <span x-text="showManualInput ? 'Sembunyikan input manual' : 'Wajah tidak terdeteksi? Input manual'"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="showManualInput && 'rotate-180'"></i>
                </button>

                <div x-show="showManualInput" x-cloak class="mt-4 p-4 rounded-xl bg-secondary/40 border border-border">
                    <label class="text-xs font-medium text-muted-foreground uppercase tracking-wide">ID Wajah Manual</label>
                    <form @submit.prevent="submitManualFace" class="flex gap-2 mt-2">
                        <input
                            x-model="manualFaceId"
                            type="text"
                            placeholder="Contoh: FACE-001"
                            class="flex-1 px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 font-mono text-sm"
                        >
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 font-medium text-sm shadow-sm"
                        >
                            Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-secondary/30 border-t border-border flex items-center gap-3 text-xs text-muted-foreground">
            <i data-lucide="credit-card" class="w-4 h-4 shrink-0"></i>
            <span>Belum scan RFID? Mulai dari <a href="{{ route('attendance.rfid') }}" class="text-primary font-medium hover:underline">check-in kartu</a>.</span>
        </div>
    </div>

    @push('scripts')
    <script>
    function faceRecognition() {
        return {
            message: '',
            messageType: '',
            userName: '',
            checkInTime: '',
            loading: false,
            showManualInput: false,
            manualFaceId: '',
            latestEventName: @json($latestEvent?->name ?? 'Tidak ada event'),

            submitFace() {
                const faceId = document.getElementById('faceInput').value.trim();
                if (!faceId) return;
                this.verifyFace(faceId);
                document.getElementById('faceInput').value = '';
            },

            submitManualFace() {
                if (!this.manualFaceId.trim()) return;
                this.verifyFace(this.manualFaceId);
                this.manualFaceId = '';
            },

            verifyFace(faceId) {
                this.loading = true;
                this.message = '';

                fetch(@json(route('attendance.face-verify')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ face_id: faceId, device_id: 'kiosk-face-01' })
                })
                .then(r => r.json())
                .then(data => {
                    this.message = data.message;
                    this.messageType = data.type;
                    this.userName = data.name || '';
                    this.checkInTime = data.time || '';
                    this.$nextTick(() => window.lucide?.createIcons());

                    if (data.success) {
                        setTimeout(() => {
                            this.message = '';
                            this.userName = '';
                            this.checkInTime = '';
                            document.getElementById('faceInput')?.focus();
                        }, 3500);
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
