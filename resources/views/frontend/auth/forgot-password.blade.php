<x-frontend::layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-white to-gray-100 dark:from-slate-900 dark:to-slate-800 px-4 py-12">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-8 space-y-6 border border-gray-200 dark:border-slate-700">
            
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Forgot Password</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-slate-400 leading-relaxed">
                    No worries! Enter your phone number and we'll send you a one-time password (OTP) to reset it.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.phone') }}" class="space-y-4">
                @csrf

                <!-- Phone Number -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 " />
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <x-primary-button class="w-full justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition-all duration-150 ease-in-out">
                        {{ __('Send Reset OTP') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    ← Back to login
                </a>
            </div>
        </div>
    </div>
</x-frontend::layout>
