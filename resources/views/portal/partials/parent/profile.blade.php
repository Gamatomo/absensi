@php $parent = $currentParent; @endphp
<div class="space-y-6 max-w-3xl">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-6 flex items-center gap-2"><x-icon name="user" class="w-5 h-5 text-primary"/>Profil Orang Tua</h2>
        <form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="text-sm text-muted-foreground">Nama</label><input name="name" value="{{ $parent['name'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Email</label><input name="email" value="{{ $parent['email'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Hubungan</label><input name="relationship" value="{{ $parent['relationship'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
                <div><label class="text-sm text-muted-foreground">Telepon</label><input name="phone" value="{{ $parent['phone'] ?? '' }}" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></div>
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
</div>
