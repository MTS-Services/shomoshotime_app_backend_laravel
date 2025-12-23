<x-admin::layout>
    <x-slot name="title">{{ __('Create Property Type') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Create Property Type') }}</x-slot>
    <x-slot name="page_slug">property-type</x-slot>
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Create Property Type') }}</h2>
                <x-admin.primary-link href="{{ route('pm.property-type.index') }}">
                    {{ __('Back') }}
                    <i data-lucide="undo-2" class="w-4 h-4"></i>
                </x-admin.primary-link>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1 {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('pm.property-type.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="space-y-2 ">
                            <p class="label">{{ __('Name') }}</p>
                            <label class="input flex items-center px-2 ">
                                <input type="text" placeholder="Name" value="{{ old('name') }}" name="name"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div class="space-y-2">
                            <p class="label">{{ __('Slug') }}</p>
                            <label class="input flex items-center gap-2">
                                <input type="text" name="slug" value="{{ old('slug') }}"
                                    placeholder="Enter slug" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Create') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>
            {{-- documentation will be loaded here and add md:col-span-2 class --}}
        </div>
    </section>
</x-admin::layout>
