@php $teacher = $currentTeacher; @endphp
<div class="space-y-6 max-w-3xl">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-6 flex items-center gap-2"><x-icon name="user" class="w-5 h-5 text-primary"/>Profil Guru</h2>
        <form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="text-sm text-muted-foreground">Nama</label><input name="name" value="{{ $teacher['name'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Email</label><input name="email" value="{{ $teacher['email'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Mata Pelajaran</label><input name="subject" value="{{ $teacher['subject'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Telepon</label><input name="phone" value="{{ $teacher['phone'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg">Simpan Perubahan</button>
        </form>
    </div>

    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-2 flex items-center gap-2"><x-icon name="lock" class="w-5 h-5 text-primary"/>Ubah Password</h2>
        <p class="text-sm text-muted-foreground mb-6">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')
            <div class="grid md:grid-cols-1 gap-4 max-w-xl">
                <div>
                    <label class="text-sm text-muted-foreground" for="update_password_current_password">Password Saat Ini</label>
                    <input id="update_password_current_password" name="current_password" type="password" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" autocomplete="current-password">
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm text-muted-foreground" for="update_password_password">Password Baru</label>
                    <input id="update_password_password" name="password" type="password" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm text-muted-foreground" for="update_password_password_confirmation">Konfirmasi Password Baru</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg">Simpan Password</button>
                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-emerald-600">Berhasil disimpan.</p>
                @endif
            </div>
        </form>
    </div>

    {{-- Face Registration Section --}}
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm" x-data="faceRegistrationData()">
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-display flex items-center gap-2"><x-icon name="scan-face" class="w-5 h-5 text-primary"/>Daftar Wajah (Pengenalan Wajah)</h2>
            @if(!empty($teacher['faceId']))
                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full flex items-center gap-1 border border-green-200">
                    <x-icon name="check-circle" class="w-3 h-3"/> Terdaftar
                </span>
            @else
                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full flex items-center gap-1 border border-yellow-200">
                    <x-icon name="alert-circle" class="w-3 h-3"/> Belum Terdaftar
                </span>
            @endif
        </div>
        <p class="text-sm text-muted-foreground mb-6">Daftarkan wajah Anda untuk absensi. Sistem memerlukan 5 sudut wajah untuk akurasi optimal.</p>
        
        <button @click="openModal" type="button" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg flex items-center gap-2">
            <x-icon name="camera" class="w-4 h-4"/> 
            @if(!empty($teacher['faceId'])) Perbarui Wajah @else Daftar Wajah @endif
        </button>

        {{-- Face Registration Modal --}}
        <div x-show="isModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div @click.away="closeModal" class="bg-card w-full max-w-2xl rounded-xl shadow-xl overflow-hidden flex flex-col">
                <div class="p-4 border-b border-border flex justify-between items-center">
                    <h3 class="font-display text-lg">Pendaftaran Wajah</h3>
                    <button @click="closeModal" type="button" class="text-muted-foreground hover:text-foreground"><x-icon name="x" class="w-5 h-5"/></button>
                </div>
                
                <div class="p-6 flex-1 flex flex-col items-center">
                    <template x-if="message">
                        <div :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="w-full mb-4 p-3 rounded-lg text-sm border" x-text="message"></div>
                    </template>

                    {{-- Capture View --}}
                    <div x-show="step < 5" class="w-full flex flex-col items-center">
                        <p class="mb-4 font-medium" x-text="instructions[step]"></p>
                        
                        <div class="relative w-64 h-64 bg-black rounded-full overflow-hidden border-4 border-primary/50 mb-6 mx-auto">
                            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover transform -scale-x-100"></video>
                            
                            {{-- Face Guide Overlay --}}
                            <div class="absolute inset-0 border-[4px] border-dashed rounded-full pointer-events-none" 
                                 :class="step === 0 ? 'border-primary' : (step === 1 ? 'border-t-primary border-transparent' : (step === 2 ? 'border-b-primary border-transparent' : (step === 3 ? 'border-l-primary border-transparent' : 'border-r-primary border-transparent')))">
                            </div>
                        </div>

                        <canvas x-ref="canvas" class="hidden" width="640" height="640"></canvas>

                        <button @click="capturePhoto" :disabled="loading" type="button" class="px-6 py-3 bg-primary text-primary-foreground rounded-full flex items-center gap-2 font-medium hover:scale-105 transition-transform disabled:opacity-50">
                            <x-icon name="camera" class="w-5 h-5"/> Ambil Foto <span x-text="(step + 1) + '/5'"></span>
                        </button>
                    </div>

                    {{-- Review View --}}
                    <div x-show="step === 5" class="w-full">
                        <p class="mb-4 font-medium text-center">Tinjau Wajah Anda</p>
                        <div class="flex gap-2 justify-center mb-6 overflow-x-auto pb-2">
                            <template x-for="(img, index) in capturedImages" :key="index">
                                <div class="relative flex-shrink-0">
                                    <img :src="img" class="w-20 h-20 rounded-lg object-cover border border-border">
                                    <p class="text-[10px] text-center mt-1 text-muted-foreground" x-text="['Depan', 'Atas', 'Bawah', 'Kiri', 'Kanan'][index]"></p>
                                </div>
                            </template>
                        </div>

                        <div class="flex gap-3 justify-center">
                            <button @click="resetCapture" type="button" class="px-4 py-2 bg-secondary text-foreground rounded-lg border border-border">Ulangi</button>
                            <button @click="submitFaces" :disabled="loading" type="button" class="px-6 py-2 bg-primary text-primary-foreground rounded-lg flex items-center gap-2">
                                <span x-show="!loading">Simpan Wajah</span>
                                <span x-show="loading">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof faceRegistrationData !== 'function') {
    function faceRegistrationData() {
        return {
            isModalOpen: false,
            stream: null,
            step: 0,
            capturedImages: [],
            loading: false,
            message: '',
            messageType: '',
            instructions: [
                "Lihat lurus ke kamera (Wajah Depan)",
                "Angkat dagu sedikit (Wajah Atas)",
                "Tundukkan kepala sedikit (Wajah Bawah)",
                "Tengok sedikit ke kiri (Wajah Kiri)",
                "Tengok sedikit ke kanan (Wajah Kanan)"
            ],

            async openModal() {
                this.isModalOpen = true;
                this.resetCapture();
                await this.startCamera();
            },

            closeModal() {
                this.isModalOpen = false;
                this.stopCamera();
            },

            async startCamera() {
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { width: 640, height: 640, facingMode: "user" } 
                    });
                    this.$refs.video.srcObject = this.stream;
                } catch (err) {
                    this.message = "Gagal mengakses kamera: " + err.message;
                    this.messageType = 'error';
                }
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
            },

            capturePhoto() {
                if (!this.stream) return;
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                const context = canvas.getContext('2d');
                
                // Draw video to canvas (flip horizontally to match mirror view)
                context.translate(canvas.width, 0);
                context.scale(-1, 1);
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                // Reset transform for next capture
                context.setTransform(1, 0, 0, 1, 0, 0);

                const imageDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                this.capturedImages.push(imageDataUrl);
                
                this.step++;
                
                if (this.step === 5) {
                    this.stopCamera();
                }
            },

            resetCapture() {
                this.step = 0;
                this.capturedImages = [];
                this.message = '';
                if (this.isModalOpen && !this.stream) {
                    this.startCamera();
                }
            },

            submitFaces() {
                this.loading = true;
                this.message = '';
                
                fetch('{{ route("face.register") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ images: this.capturedImages })
                })
                .then(res => res.json())
                .then(data => {
                    this.message = data.message;
                    this.messageType = data.success ? 'success' : 'error';
                    if (data.success) {
                        setTimeout(() => {
                            this.closeModal();
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(err => {
                    this.message = "Terjadi kesalahan server: " + err.message;
                    this.messageType = 'error';
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        }
    }
}
</script>
