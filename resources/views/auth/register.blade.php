<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-2 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Posisi')" />
            <select id="role" name="role" class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3.5 py-2.5" required>
                <option value="" disabled selected>Pilih Posisi</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Siswa</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Guru</option>
                <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Orang Tua</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4 border-t border-gray-200 mt-6 pt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Daftar') }}
            </x-primary-button>

            <p class="text-center text-sm text-gray-600">
                {{ __('Sudah Punya Akun?') }}
                <a class="underline font-medium text-indigo-600 hover:text-indigo-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-1" href="{{ route('login') }}">
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
