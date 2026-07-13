<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-6 text-sm text-gray-600 leading-relaxed">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4 border-t border-gray-200 mt-6 pt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>

            <a class="block text-center text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Kembali ke halaman masuk') }}
            </a>
        </div>
    </form>
</x-guest-layout>
