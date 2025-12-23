<x-frontend::layout>
    <x-slot name="title">{{ __('Home') }}</x-slot>
    <x-slot name="page_slug">{{ __('home') }}</x-slot>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ config('app.name', 'Dashboard') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    @if (Auth::guard()->check())
                        @if (user()->is_admin)
                            {{ __('Welcome, Admin!') }}
                        @else
                            {{ __(' Welcome, User!') }}
                        @endif
                    @else
                        {{ __('Please select your login portal') }}
                    @endif
                </p>
            </div>

            <div class="space-y-4">
                {{-- <button id="test-sms-send"
                    class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                    Test SMS Send
                </button> --}}

                @if (Auth::guard()->check())
                    @if (user()->is_admin)
                        {{-- Admin Dashboard --}}
                        <a href="{{ url('/admin/dashboard') }}"
                            class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                            <div
                                class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : 'space-x-4' }}">
                                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                    <i data-lucide="layout-dashboard"
                                        class="w-6 h-6 text-indigo-600 dark:text-indigo-300"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Go to Admin Dashboard') }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Access your admin panel') }}</p>
                                </div>
                                <div
                                    class="{{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }} text-indigo-600 dark:text-indigo-400">
                                    <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}"
                                        class="w-5 h-5"></i>
                                </div>
                            </div>
                        </a>

                        {{-- Admin Logout --}}
                        <form method="POST" action="{{ url('/logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-red-500 dark:hover:border-red-400 text-left">
                                <div
                                    class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : 'space-x-4' }}">
                                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                                        <i data-lucide="log-out" class="w-6 h-6 text-red-600 dark:text-red-300"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ __('Logout from Admin') }}
                                        </h2>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            {{ __('Sign out of your admin account') }}</p>
                                    </div>
                                    <div
                                        class="{{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }} text-red-600 dark:text-red-400">
                                        <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}"
                                            class="w-5 h-5"></i>
                                    </div>
                                </div>
                            </button>
                        </form>
                    @else
                        {{-- User Dashboard --}}
                        <a href="{{ url('user/dashboard') }}"
                            class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                            <div
                                class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : 'space-x-4' }}">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                    <i data-lucide="user" class="w-6 h-6 text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Go to User Dashboard') }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Access your user panel') }}</p>
                                </div>
                                <div
                                    class="{{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }} text-blue-600 dark:text-gray-600">
                                    <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}"
                                        class="w-5 h-5"></i>
                                </div>
                            </div>
                        </a>

                        {{-- User Logout --}}
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-red-500 dark:hover:border-red-400 text-left">
                                <div
                                    class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : 'space-x-4' }}">
                                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                                        <i data-lucide="log-out" class="w-6 h-6 text-red-600 dark:text-red-300"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ __('Logout') }}</h2>
                                        <p class="text-gray-600 dark:text-gray-400">
                                            {{ __('Sign out of your account') }}
                                        </p>
                                    </div>
                                    <div
                                        class="{{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }} text-red-600 dark:text-red-400">
                                        <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}"
                                            class="w-5 h-5"></i>
                                    </div>
                                </div>
                            </button>
                        </form>
                    @endif
                @else
                    {{-- User Login --}}


                    {{-- Register --}}


                    {{-- Admin Login --}}
                    <a href="{{ route('login') }}"
                        class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                        <div
                            class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse space-x-reverse' : 'space-x-4' }}">
                            <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                <i data-lucide="user-cog" class="w-6 h-6 text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white ">
                                    {{ __('Admin Login') }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ __('Access admin control panel') }}</p>
                            </div>
                            <div
                                class=" {{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }} text-indigo-600 dark:text-indigo-400">
                                <i data-lucide="{{ app()->getLocale() === 'ar' ? 'arrow-left' : 'arrow-right' }}"
                                    class="w-5 h-5"></i>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sms = document.getElementById('test-sms-send');

            sms.addEventListener('click', function(e) {
                const route = "{{ route('sendSms') }}";

                axios.post(route).then(function(response) {
                    console.log(response.data);
                }).catch(function(error) {
                    console.error(error);
                });
            });
        });
    </script>
</x-frontend::layout>
