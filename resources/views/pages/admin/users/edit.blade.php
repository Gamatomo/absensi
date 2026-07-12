<x-layouts.app title="Edit User - {{ $user->name }}">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-chart-3/10 border border-chart-3/30 text-chart-3 flex items-center gap-3">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0"/>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-chart-5/10 border border-chart-5/30 text-chart-5 flex items-center gap-3">
            <x-icon name="alert-circle" class="w-5 h-5 shrink-0"/>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Back Button --}}
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4"/>
            Kembali
        </a>

        {{-- User Info Card --}}
        <x-ui.card title="Informasi User" subtitle="Detail akun pengguna.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                    <x-icon name="user" class="w-5 h-5 text-muted-foreground"/>
                    <div>
                        <p class="text-xs text-muted-foreground">Nama</p>
                        <p class="font-medium">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                    <x-icon name="mail" class="w-5 h-5 text-muted-foreground"/>
                    <div>
                        <p class="text-xs text-muted-foreground">Email</p>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                    <x-icon name="shield" class="w-5 h-5 text-muted-foreground"/>
                    <div>
                        <p class="text-xs text-muted-foreground">Role</p>
                        <p class="font-medium capitalize">{{ $user->role }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                    <x-icon name="activity" class="w-5 h-5 text-muted-foreground"/>
                    <div>
                        <p class="text-xs text-muted-foreground">Status</p>
                        <p class="font-medium">
                            @if($user->is_active)
                                <span class="text-chart-3">Aktif</span>
                            @else
                                <span class="text-chart-5">Nonaktif</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if($user->student)
                    <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                        <x-icon name="id-card" class="w-5 h-5 text-muted-foreground"/>
                        <div>
                            <p class="text-xs text-muted-foreground">NIS</p>
                            <p class="font-medium">{{ $user->student->student_number ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                        <x-icon name="building-2" class="w-5 h-5 text-muted-foreground"/>
                        <div>
                            <p class="text-xs text-muted-foreground">Jurusan</p>
                            <p class="font-medium">{{ $user->student->department ?? '-' }}</p>
                        </div>
                    </div>
                @endif
                @if($user->teacher)
                    <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                        <x-icon name="id-card" class="w-5 h-5 text-muted-foreground"/>
                        <div>
                            <p class="text-xs text-muted-foreground">NIP</p>
                            <p class="font-medium">{{ $user->teacher->teacher_number ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-secondary/50 rounded-lg border border-border">
                        <x-icon name="book-open" class="w-5 h-5 text-muted-foreground"/>
                        <div>
                            <p class="text-xs text-muted-foreground">Mata Pelajaran</p>
                            <p class="font-medium">{{ $user->teacher->subject ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- RFID Card Management --}}
        <x-ui.card title="Kartu RFID" subtitle="Kelola kartu RFID untuk absensi.">
            {{-- Active Card Display --}}
            @php
                $activeCard = $user->rfidCards->where('status', 'active')->first();
            @endphp

            @if($activeCard)
                <div class="mb-6 p-5 rounded-xl bg-primary/5 border-2 border-primary/20">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-primary/10 p-3 rounded-lg border border-primary/20">
                                <x-icon name="credit-card" class="w-6 h-6 text-primary"/>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-1">Kartu Aktif</p>
                                <p class="font-mono text-lg font-bold text-primary tracking-wider">{{ $activeCard->uid }}</p>
                                <p class="text-xs text-muted-foreground mt-1">Didaftarkan {{ $activeCard->assigned_at?->locale('id')->diffForHumans() ?? '-' }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.users.revoke-rfid', [$user->id, $activeCard->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin mencabut kartu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-chart-5/10 text-chart-5 border border-chart-5/30 rounded-lg hover:bg-chart-5/20 transition-colors cursor-pointer">
                                <x-icon name="x-circle" class="w-4 h-4"/>
                                Cabut Kartu
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Assign New Card --}}
            <div x-data="rfidAssign()" class="space-y-4">
                <h4 class="text-sm font-medium flex items-center gap-2">
                    <x-icon name="plus-circle" class="w-4 h-4 text-primary"/>
                    {{ $activeCard ? 'Ganti Kartu RFID' : 'Daftarkan Kartu RFID' }}
                </h4>

                {{-- Mode Switcher --}}
                <div class="flex gap-2">
                    <button
                        @click="mode = 'manual'"
                        :class="mode === 'manual' ? 'bg-primary text-primary-foreground' : 'bg-secondary text-muted-foreground hover:text-foreground'"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg border border-border transition-all cursor-pointer"
                    >
                        <x-icon name="keyboard" class="w-4 h-4"/>
                        Ketik Manual
                    </button>
                    <button
                        @click="mode = 'scan'; $nextTick(() => $refs.uidInput.focus())"
                        :class="mode === 'scan' ? 'bg-primary text-primary-foreground' : 'bg-secondary text-muted-foreground hover:text-foreground'"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg border border-border transition-all cursor-pointer"
                    >
                        <x-icon name="scan" class="w-4 h-4"/>
                        Mode Scan
                    </button>
                </div>

                {{-- Input Form --}}
                <form action="{{ route('admin.users.assign-rfid', $user->id) }}" method="POST" @submit="onSubmit" x-ref="form">
                    @csrf
                    <div class="relative">
                        {{-- Scan Mode Overlay --}}
                        <div
                            x-show="mode === 'scan'"
                            x-transition
                            class="mb-4 p-6 rounded-xl border-2 border-dashed border-primary/40 bg-primary/5 text-center"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <div class="relative">
                                    <div class="bg-primary/10 p-4 rounded-full border border-primary/20 animate-pulse">
                                        <x-icon name="scan" class="w-8 h-8 text-primary"/>
                                    </div>
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3" x-show="mode === 'scan'">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                                    </span>
                                </div>
                                
                                {{-- Cloud Connection Status --}}
                                <div class="w-full max-w-sm mx-auto">
                                    <p class="text-sm font-medium text-primary">Menunggu scan kartu dari scanner...</p>
                                    <p class="text-xs text-muted-foreground mt-1">Otomatis tersimpan setelah kartu terbaca.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Input Field --}}
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <input
                                    x-ref="uidInput"
                                    type="text"
                                    name="uid"
                                    x-model="uid"
                                    :placeholder="mode === 'scan' ? 'Kartu akan terdeteksi otomatis...' : 'Masukkan UID kartu RFID (contoh: AB12CD34)'"
                                    class="w-full px-4 py-3 rounded-lg border border-border bg-card text-foreground font-mono tracking-wider text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary placeholder:text-muted-foreground/50"
                                    :class="mode === 'scan' ? 'border-primary/30 bg-primary/5' : ''"
                                    autocomplete="off"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors cursor-pointer"
                            >
                                <x-icon name="save" class="w-4 h-4"/>
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
                @error('uid')
                    <p class="text-sm text-chart-5 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Card History --}}
            @php
                $revokedCards = $user->rfidCards->where('status', '!=', 'active');
            @endphp
            @if($revokedCards->count() > 0)
                <div class="mt-6 pt-6 border-t border-border">
                    <h4 class="text-sm font-medium text-muted-foreground mb-3 flex items-center gap-2">
                        <x-icon name="history" class="w-4 h-4"/>
                        Riwayat Kartu
                    </h4>
                    <div class="space-y-2">
                        @foreach($revokedCards as $card)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-secondary/30 border border-border/50">
                                <div class="flex items-center gap-3">
                                    <x-icon name="credit-card" class="w-4 h-4 text-muted-foreground"/>
                                    <span class="font-mono text-sm text-muted-foreground">{{ $card->uid }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs px-2 py-1 rounded bg-chart-5/10 text-chart-5 border border-chart-5/20 uppercase">{{ $card->status }}</span>
                                    @if($card->revoked_at)
                                        <span class="text-xs text-muted-foreground">{{ $card->revoked_at->locale('id')->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-ui.card>

        {{-- Face Profile Status --}}
        <x-ui.card title="Profil Wajah" subtitle="Status pendaftaran wajah untuk absensi.">
            @php
                $faceProfile = $user->faceProfiles->first();
            @endphp
            @if($faceProfile)
                <div class="flex items-center gap-4 p-4 rounded-lg bg-chart-3/5 border border-chart-3/20">
                    <div class="bg-chart-3/10 p-3 rounded-lg border border-chart-3/20">
                        <x-icon name="scan-face" class="w-6 h-6 text-chart-3"/>
                    </div>
                    <div>
                        <p class="font-medium text-chart-3">Wajah Terdaftar</p>
                        <p class="text-xs text-muted-foreground">Didaftarkan {{ $faceProfile->created_at?->locale('id')->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-4 p-4 rounded-lg bg-chart-4/5 border border-chart-4/20">
                    <div class="bg-chart-4/10 p-3 rounded-lg border border-chart-4/20">
                        <x-icon name="scan-face" class="w-6 h-6 text-chart-4"/>
                    </div>
                    <div>
                        <p class="font-medium text-chart-4">Belum Terdaftar</p>
                        <p class="text-xs text-muted-foreground">User ini belum mendaftarkan wajah untuk absensi.</p>
                    </div>
                </div>
            @endif
        </x-ui.card>
    </div>

    @push('scripts')
    <script>
        function rfidAssign() {
            return {
                mode: 'manual',
                uid: '',
                scanInterval: null,
                
                init() {
                    this.$watch('mode', value => {
                        if (value === 'scan') {
                            this.startScanPolling();
                        } else {
                            this.stopScanPolling();
                        }
                    });
                },
                
                startScanPolling() {
                    this.stopScanPolling();
                    console.log('Started polling for RFID scans...');
                    // Poll the cloud API every 1.5 seconds for new scans
                    this.scanInterval = setInterval(() => {
                        fetch('/api/v1/device/last-scan', { cache: 'no-store' })
                            .then(r => r.json())
                            .then(data => {
                                if (data.uid) {
                                    console.log('Received UID:', data.uid);
                                    this.uid = data.uid.toUpperCase();
                                    this.stopScanPolling(); // Stop polling while we submit
                                    
                                    // Give a 1-second delay so the user can see the ID before the page reloads
                                    setTimeout(() => {
                                        const submitBtn = this.$refs.form.querySelector('button[type="submit"]');
                                        if (submitBtn) {
                                            submitBtn.click();
                                        } else {
                                            this.$refs.form.submit();
                                        }
                                    }, 1000);
                                }
                            })
                            .catch(e => console.error('Cloud scan check failed:', e));
                    }, 1500);
                },
                
                stopScanPolling() {
                    if (this.scanInterval) {
                        clearInterval(this.scanInterval);
                        this.scanInterval = null;
                    }
                },

                onSubmit(e) {
                    if (!this.uid.trim()) {
                        e.preventDefault();
                        return;
                    }
                    // Convert to uppercase
                    this.uid = this.uid.trim().toUpperCase();
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
