<x-frontend::layout>
    <x-slot name="title">{{ __('Home') }}</x-slot>
    <x-slot name="page_slug">{{ __('home') }}</x-slot>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ config('app.name', 'Laravel Application') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    {{ __('Welcome to the Laravel Application') }}
                </p>
            </div>
        </div>
    </div>
</x-frontend::layout>
