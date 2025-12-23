<x-frontend::layout>
    <div class="h-screen flex items-center justify-center bg-white dark:bg-slate-900">
        <div class="max-w-md w-full space-y-8 shadow-md rounded-md p-6 bg-gray-100 dark:bg-slate-800">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-800 dark:text-white">Reset Password</h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Enter your new password below.
            </p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="phone" value="{{ $phone }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mt-4">
                    <x-input-error :messages="$errors->get('token')" class="mt-2 text-red-600 dark:text-red-400" />
                </div>

                <!-- New Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300" />
                    <x-text-input id="password" class="block mt-1 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                        type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 dark:text-red-400" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300" />
                    <x-text-input id="password_confirmation"
                        class="block mt-1 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 dark:text-red-400" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-frontend::layout>
