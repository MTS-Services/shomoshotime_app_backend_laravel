<x-frontend::layout>
    <div class="h-screen flex items-center justify-center bg-gray-100 dark:bg-slate-900">
        <div class="max-w-md w-full space-y-8 shadow-md rounded-md p-6 bg-white dark:bg-slate-800">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Admin Login') }}
                </p>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input 
                        id="email" 
                        class="block mt-1 w-full text-gray-900 dark:text-gray-100 bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-600 placeholder-gray-400 dark:placeholder-gray-300"
                        type="email" 
                        name="email"
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username" 
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input 
                        id="password" 
                        class="block mt-1 w-full text-gray-900 dark:text-gray-100 bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-600 placeholder-gray-400 dark:placeholder-gray-300"
                        type="password" 
                        name="password"
                        required 
                        autocomplete="current-password" 
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox"
                            class="rounded-sm border-gray-300 dark:border-slate-600 text-indigo-600 shadow-xs focus:ring-indigo-500 bg-white dark:bg-slate-700"
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <input type="hidden" name="fcm_token" id="fcmTokenInput">

                <!-- Actions -->
                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-400 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button id="loginButton" class="ms-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script type="module">
            import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
            import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

            const firebaseConfig = {
                apiKey: "AIzaSyAHRdYjEG3k1JzYR7OW31bLfC71qi0UNCY",
                authDomain: "skywalker-notification.firebaseapp.com",
                projectId: "skywalker-notification",
                storageBucket: "skywalker-notification.firebasestorage.app",
                messagingSenderId: "624087602629",
                appId: "1:624087602629:web:e0bd6c7aaef5ccea2c27ac",
                measurementId: "G-QZWS5CXB81"
            };

            const vapidKey = "BMP4uIYiZZxGFnZWbQR5Ak93lcODHEZedo8A19Lpm7CV3OG31oE5a6aSmF0c6XnFHAxbN0C19b2TWZv6aUaF8uA";

            try {
                const app = initializeApp(firebaseConfig);
                var messaging = getMessaging(app);
            } catch (e) {
                console.error("Firebase initialization failed.", e);
            }

            const loginForm = document.getElementById('loginForm');
            const fcmTokenInput = document.getElementById('fcmTokenInput');
            const loginButton = document.getElementById('loginButton');

            function toggleButtonState(isProcessing) {
                if (isProcessing) {
                    loginButton.disabled = true;
                    loginButton.innerHTML = `<span class="loader"></span> Generating token...`;
                    loginButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    loginButton.disabled = false;
                    loginButton.innerHTML = `{{ __('Log in') }}`;
                    loginButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            loginForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                toggleButtonState(true);

                if (fcmTokenInput.value) {
                    loginForm.submit();
                    return;
                }

                try {
                    if (!messaging) {
                        loginForm.submit();
                        return;
                    }

                    const swRegistration = await navigator.serviceWorker.register('./firebase.js');
                    const permission = await Notification.requestPermission();

                    if (permission === 'granted') {
                        const currentToken = await getToken(messaging, {
                            vapidKey: vapidKey,
                            serviceWorkerRegistration: swRegistration
                        });

                        fcmTokenInput.value = currentToken ?? '';
                        loginForm.submit();
                    } else {
                        loginForm.submit();
                    }
                } catch (error) {
                    console.error('Error getting FCM token:', error);
                    loginForm.submit();
                } finally {
                    toggleButtonState(false);
                }
            });
        </script>
    @endpush
</x-frontend::layout>
