<x-admin::layout>
    <x-slot name="title">{{ __('Company Information') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Company Information') }}</x-slot>
    <x-slot name="page_slug">company - information</x-slot>

    {{-- @dd($companyInfo) --}}
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Company Information') }}</h2>
                <x-admin.primary-link href="{{ route('user-profile') }}">{{ __('Back') }} <i data-lucide="undo-2"
                        class="w-4 h-4"></i> </x-admin.primary-link>
            </div>
        </div>
        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1  {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <!-- Form Section -->
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div class="glass-card rounded-2xl p-6 md:col-span-5 mb-6">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <!-- Name -->
                            <div class="space-y-2">
                                <p class="label">{{ __('Name') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->company_name ?? '-' }}
                                </div>
                            </div>

                            <!-- Name (Arabic) -->
                            <div class="space-y-2">
                                <p class="label">{{ __('Name (Arabic)') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->company_name_ar ?? '-' }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2 sm:col-span-2">
                                <p class="label">{{ __('Description') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->company_description ?? '-' }}
                                </div>
                            </div>

                            <!-- Description (Arabic) -->
                            <div class="space-y-2 sm:col-span-2">
                                <p class="label">{{ __('Description (Arabic)') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->company_description_ar ?? '-' }}
                                </div>
                            </div>

                            <!-- Address (English) -->
                            <div class="space-y-2">
                                <p class="label">{{ __('Address (English)') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->address_en ?? '-' }}
                                </div>
                            </div>

                            <!-- Address (Arabic) -->
                            <div class="space-y-2">
                                <p class="label">{{ __('Address (Arabic)') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    {{ $companyInfo->address_ar ?? '-' }}
                                </div>
                            </div>

                            <!-- Website -->
                            <div class="space-y-2">
                                <p class="label">{{ __('Website') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1 break-all">
                                    <a href="{{ $companyInfo->website }}" class="text-blue-600 hover:underline"
                                        target="_blank">
                                        {{ $companyInfo->website ?? '-' }}
                                    </a>
                                </div>
                            </div>

                            <!-- Social Links -->
                            <div class="space-y-2 sm:col-span-2">
                                <p class="label">{{ __('Social Links') }}</p>
                                <div class="px-2 py-2 bg-gray-200 rounded flex-1">
                                    @forelse ($companyInfo->social_links_arr as $mediaName => $url)
                                        <div>
                                            <span class="font-semibold">{{ ucfirst($mediaName) }}:</span>
                                            <a href="{{ $url }}" class="text-blue-600 hover:underline"
                                                target="_blank">{{ $url }}</a>
                                        </div>
                                    @empty
                                        <span>-</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</x-admin::layout>
