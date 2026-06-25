@php
    $roleLabels = [
        'student' => 'Siswa',
        'teacher' => 'Guru',
        'parent' => 'Orang Tua',
    ];
    $studentsForSelect = collect($students)->filter(fn ($s) => !empty($s['dbId']))->values();
@endphp
<div class="space-y-6" x-data="userVerificationAdminData()">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-amber-500/10 rounded-lg"><x-icon name="user-check" class="w-5 h-5 text-amber-600"/></div>
                <div>
                    <h2 class="font-display">Verifikasi Pengguna</h2>
                    <p class="text-sm text-muted-foreground">{{ count($pendingUsers) }} pengguna menunggu verifikasi</p>
                </div>
            </div>
            @if(count($pendingUsers) > 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-chart-4/10 text-chart-4 border border-chart-4/30">
                {{ count($pendingUsers) }} pending
            </span>
            @endif
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"/>
                <input x-model="searchQuery" class="w-full pl-11 pr-4 py-3 border border-border rounded-lg bg-background" placeholder="Cari nama atau email...">
            </div>
            <select x-model="filterRole" class="px-4 py-3 border border-border rounded-lg bg-background">
                <option value="all">Semua Peran</option>
                <option value="student">Siswa</option>
                <option value="teacher">Guru</option>
                <option value="parent">Orang Tua</option>
            </select>
        </div>
    </div>

    @if(count($pendingUsers) === 0)
    <div class="bg-card border border-border rounded-lg p-12 text-center shadow-sm">
        <x-icon name="check-circle-2" class="w-12 h-12 text-chart-3 mx-auto mb-4"/>
        <h3 class="font-display mb-2">Tidak ada pengguna menunggu</h3>
        <p class="text-sm text-muted-foreground">Semua pendaftaran telah diverifikasi.</p>
    </div>
    @endif

    <div class="space-y-4">
        @foreach($pendingUsers as $pendingUser)
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm"
             x-show="(filterRole==='all'||filterRole==='{{ $pendingUser['role'] }}') && (!searchQuery || '{{ strtolower($pendingUser['name'].' '.$pendingUser['email']) }}'.includes(searchQuery.toLowerCase()))">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="font-display">{{ $pendingUser['name'] }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">{{ $roleLabels[$pendingUser['role']] ?? $pendingUser['role'] }}</span>
                    </div>
                    <p class="text-sm text-muted-foreground">{{ $pendingUser['email'] }}</p>
                    <p class="text-sm text-muted-foreground mt-1">
                        Daftar: {{ \Carbon\Carbon::parse($pendingUser['registeredAt'])->locale('id')->isoFormat('D MMM YYYY HH:mm') }}
                    </p>
                    <p class="text-sm mt-2 {{ $pendingUser['hasProfile'] ? 'text-chart-3' : 'text-chart-4' }}">
                        {{ $pendingUser['hasProfile'] ? 'Profil terhubung ke data' : 'Belum terhubung ke data' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                            data-user-id="{{ $pendingUser['id'] }}"
                            data-user-role="{{ $pendingUser['role'] }}"
                            data-user-name="{{ $pendingUser['name'] }}"
                            @click="openApproveModal($event)"
                            class="px-3 py-1.5 bg-chart-3/10 text-chart-3 rounded-lg text-sm border border-chart-3/30 disabled:opacity-50">
                        Setujui
                    </button>
                    <button type="button"
                            data-user-id="{{ $pendingUser['id'] }}"
                            @click="rejectUser($event)"
                            class="px-3 py-1.5 bg-chart-5/10 text-chart-5 rounded-lg text-sm border border-chart-5/30 disabled:opacity-50">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Approve modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="closeModal()">
        <div class="bg-card border border-border rounded-lg shadow-lg w-full max-w-md p-6" @click.outside="closeModal()">
            <h3 class="font-display mb-1">Verifikasi Pengguna</h3>
            <p class="text-sm text-muted-foreground mb-4" x-text="modalUserName"></p>

            <div class="space-y-4">
                <template x-if="modalUserRole === 'student'">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-muted-foreground">Jurusan (opsional)</label>
                            <input x-model="form.department" type="text" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Contoh: Teknik Informatika">
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">NISN (opsional)</label>
                            <input x-model="form.nisn" type="text" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Nomor NISN">
                        </div>
                    </div>
                </template>

                <template x-if="modalUserRole === 'teacher'">
                    <div>
                        <label class="text-sm text-muted-foreground">Mata Pelajaran (opsional)</label>
                        <input x-model="form.subject" type="text" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Contoh: Matematika">
                    </div>
                </template>

                <template x-if="modalUserRole === 'parent'">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-muted-foreground">Siswa <span class="text-chart-5">*</span></label>
                            <select x-model="form.student_id" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" required>
                                <option value="">Pilih siswa</option>
                                @foreach($studentsForSelect as $student)
                                <option value="{{ $student['dbId'] }}">{{ $student['name'] }} ({{ $student['id'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">Hubungan <span class="text-chart-5">*</span></label>
                            <select x-model="form.relationship" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" required>
                                <option value="">Pilih hubungan</option>
                                <option value="Ayah">Ayah</option>
                                <option value="Ibu">Ibu</option>
                                <option value="Wali">Wali</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-muted-foreground">Pekerjaan (opsional)</label>
                            <input x-model="form.occupation" type="text" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Pekerjaan">
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="button" @click="closeModal()" class="flex-1 px-4 py-2 border border-border rounded-lg hover:bg-secondary">Batal</button>
                <button type="button" @click="approveUser()" :disabled="processing" class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded-lg disabled:opacity-50">
                    <span x-show="!processing">Verifikasi</span>
                    <span x-show="processing">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function userVerificationAdminData() {
    return {
        searchQuery: '',
        filterRole: 'all',
        processing: false,
        showModal: false,
        modalUserId: null,
        modalUserRole: null,
        modalUserName: '',
        form: {
            department: '',
            nisn: '',
            subject: '',
            student_id: '',
            relationship: '',
            occupation: '',
        },

        resetForm() {
            this.form = {
                department: '',
                nisn: '',
                subject: '',
                student_id: '',
                relationship: '',
                occupation: '',
            };
        },

        openApproveModal(event) {
            const btn = event.currentTarget;
            this.modalUserId = btn.dataset.userId;
            this.modalUserRole = btn.dataset.userRole;
            this.modalUserName = btn.dataset.userName;
            this.resetForm();
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.modalUserId = null;
            this.modalUserRole = null;
            this.modalUserName = '';
            this.resetForm();
        },

        approveUser() {
            if (this.modalUserRole === 'parent' && (!this.form.student_id || !this.form.relationship)) {
                alert('Orang tua harus dihubungkan ke siswa dan hubungan keluarga');
                return;
            }

            this.processing = true;
            const body = {};
            if (this.form.department) body.department = this.form.department;
            if (this.form.nisn) body.nisn = this.form.nisn;
            if (this.form.subject) body.subject = this.form.subject;
            if (this.form.student_id) body.student_id = parseInt(this.form.student_id, 10);
            if (this.form.relationship) body.relationship = this.form.relationship;
            if (this.form.occupation) body.occupation = this.form.occupation;

            fetch(`/users/${this.modalUserId}/approve`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    alert('Gagal: ' + data.message);
                    this.processing = false;
                }
            })
            .catch(e => {
                alert('Error: ' + e.message);
                this.processing = false;
            });
        },

        rejectUser(event) {
            if (!confirm('Yakin ingin menolak pendaftaran pengguna ini?')) return;

            const id = event.currentTarget.dataset.userId;
            this.processing = true;

            fetch(`/users/${id}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    alert('Gagal: ' + data.message);
                    this.processing = false;
                }
            })
            .catch(e => {
                alert('Error: ' + e.message);
                this.processing = false;
            });
        }
    };
}
</script>
