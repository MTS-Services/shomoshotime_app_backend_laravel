<x-admin::layout>
    <x-slot name="title">{{ __('Edit Area') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Edit Area') }}</x-slot>
    <x-slot name="page_slug">area</x-slot> {{-- Changed page_slug --}}
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Edit Area') }}</h2>
                <x-admin.primary-link href="{{ route('area.index') }}">{{ __('Back') }} <i data-lucide="undo-2"
                        class="w-4 h-4"></i> </x-admin.primary-link>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1 {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                {{-- Form action points to update route, includes admin ID --}}
                <form action="{{ route('area.update', encrypt($area->id)) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Method spoofing for PUT request --}}

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="space-y-2 ">
                            <p class="label">{{ __('Name') }}</p>
                            <label class="input flex items-center px-2 ">
                                <input type="text" placeholder="Name" value="{{ old('name', $area->name) }}"
                                    name="name" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="space-y-2">
                            <p class="label">{{ __('Slug') }}</p>
                            <label class="input flex items-center gap-2">
                                <input type="slug" name="slug" value="{{ old('slug', $area->slug) }}"
                                    placeholder="Enter slug" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Update') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>

            {{-- documentation will be loaded here and add md:col-span-2 class --}}

        </div>
    </section>
</x-admin::layout>
