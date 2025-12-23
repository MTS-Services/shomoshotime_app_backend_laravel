<x-admin::layout>
    <x-slot name="title">{{ __('Admin Dashboard') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Dashboard') }}</x-slot>
    <x-slot name="page_slug">admin-dashboard</x-slot>

    <section>
        {{-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6" --}}
        <div class=" container mx-auto"
            x-transition:enter="transition-all duration-500" x-transition:enter-start="opacity-0 translate-y-8"
            x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white  dark:bg-gray-900 text-white font-sans">
                <div class="flex items-center justify-center h-auto lg:min-h-[80vh] p-4">
                    <div class="text-center">
                        <h1 class="text-5xl md:text-7xl font-bold text-bg-black dark:text-white mb-4">Coming Soon</h1>
                        <p class="text-lg md:text-xl text-gray-400">We're working hard to bring you something amazing.
                            Stay tuned!</p>
                        {{-- <form class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-2">
                            <input type="email" placeholder="Enter your email"
                                class="p-3 w-full sm:w-80 rounded-lg bg-gray-800 text-white border border-gray-700 focus:outline-none focus:border-blue-500">
                            <button type="submit"
                                class="w-full sm:w-auto p-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition duration-300 ease-in-out">Notify
                                Me</button>
                        </form> --}}
                    </div>
                </div>
            </div>
            {{-- <div class="glass-card rounded-2xl p-6 card-hover float interactive-card relative flex items-center justify-center" style="animation-delay: 0.2s;">
                <a href="{{ route('admin.sayHi') }}" class="absolute inset-0 flex items-center justify-center"></a>
                <p>Say Hi</p>
            </div>


            <div class="glass-card rounded-2xl p-6 card-hover float interactive-card" style="animation-delay: 0s;"
                @click="showDetails('users')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <div class="text-green-400 text-sm font-medium flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                        +12%
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-text-white mb-1">12,384</h3>
                <p class="text-gray-800/60 dark:text-text-dark-primary text-sm">{{ __('Total Users') }}</p>
                <div class="mt-4 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full progress-bar"
                        style="width: 75%;"></div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 card-hover float interactive-card" style="animation-delay: 0.2s;"
                @click="showDetails('revenue')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-green-400"></i>
                    </div>
                    <div class="text-green-400 text-sm font-medium flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                        +23%
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800/60 dark:text-text-dark-primary mb-1">$<span>48,392</span>
                </h3>
                <p class="text-gray-800/60 dark:text-text-dark-primary text-sm">{{ __('Total Revenue') }}</p>
                <div class="mt-4 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full progress-bar"
                        style="width: 60%;"></div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 card-hover float interactive-card" style="animation-delay: 0.4s;"
                @click="showDetails('orders')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-6 h-6 text-purple-400"></i>
                    </div>
                    <div class="text-red-400 text-sm font-medium flex items-center gap-1">
                        <i data-lucide="trending-down" class="w-3 h-3"></i>
                        -5%
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-text-white mb-1">2,847</h3>
                <p class="text-gray-800/60 dark:text-text-dark-primary text-sm">{{ __('Total Orders') }}</p>
                <div class="mt-4 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-purple-400 to-purple-600 rounded-full progress-bar"
                        style="width: 45%;"></div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 card-hover float interactive-card" style="animation-delay: 0.6s;"
                @click="showDetails('active')">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="activity" class="w-6 h-6 text-yellow-400"></i>
                    </div>
                    <div class="text-yellow-400 text-sm font-medium flex items-center gap-1">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                        {{ __('Live') }}
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-text-white mb-1">847</h3>
                <p class="text-gray-800/60 dark:text-text-dark-primary text-sm">{{ __('Active Users') }}</p>
                <div class="mt-4 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full pulse-slow progress-bar"
                        style="width: 85%;"></div>
                </div>
            </div> --}}
        </div>
    </section>
</x-admin::layout>
