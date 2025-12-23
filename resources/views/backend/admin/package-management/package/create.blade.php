<x-admin::layout>
    <x-slot name="title">{{ __('Create Package') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Create Package') }}</x-slot>
    <x-slot name="page_slug">package</x-slot>
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Create Package') }}</h2>
                <x-admin.primary-link href="{{ route('pam.package.index') }}">{{ __('Back') }} <i data-lucide="undo-2"
                        class="w-4 h-4"></i> </x-admin.primary-link>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1 {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('pam.package.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="space-y-3 ">
                            <p class="label">{{ __('Tag') }}</p>
                            <label class="input flex items-center px-2 ">
                                <select name="tag" class="select" id="tag">
                                    <option value="" selected hidden>{{ __('Select Tag') }}</option>
                                    @foreach (App\Models\Package::tagList() as $key => $tag)
                                        <option value="{{ $key }}" {{ old('tag') == $key ? 'selected' : '' }}>
                                            {{ $tag }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('tag')" />
                        </div>
                        <div class="space-y-3">
                            <p class="label">{{ __('Total Ad') }}</p>
                            <label class="input flex items-center gap-2">
                                <input type="text" name="total_ad" value="{{ old('total_ad') }}"
                                    placeholder="Enter total ad" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('total_ad')" />
                        </div>
                        <div class="space-y-3">
                            <p class="label">{{ __('Price') }}</p>
                            <label class="input flex items-center gap-2">
                                <input type="text" name="price" value="{{ old('price') }}"
                                    placeholder="Enter price" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Create') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>

            {{-- documentation will be loded here and add md:col-span-2 class --}}

        </div>
    </section>
</x-admin::layout>
