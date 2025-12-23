<x-admin::layout>
    <x-slot name="title">{{ __('Edit Category') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Edit Category') }}</x-slot>
    <x-slot name="page_slug">category</x-slot>
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Edit Category') }}</h2>
                <x-admin.primary-link href="{{ route('category.index') }}">
                    {{ __('Back') }} <i data-lucide="undo-2" class="w-4 h-4"></i>
                </x-admin.primary-link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-1">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('category.update', encrypt($category->id)) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="space-y-2">
                            <p class="label">{{ __('Name') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" placeholder="Name" value="{{ old('name', $category->name) }}"
                                    name="name" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div class="space-y-2">
                            <p class="label">{{ __('Name (Arabic)') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" placeholder="Name (Arabic)"
                                    value="{{ old('name_ar', $category->name_ar) }}" name="name_ar" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
                        </div>

                        <div class="space-y-2">
                            <p class="label">{{ __('Slug') }}</p>
                            <label class="input flex items-center gap-2">
                                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                                    placeholder="Enter slug" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>

                        <div class="space-y-2 sm:col-span-2">
                            <p class="label">{{ __('Description') }}</p>
                            <textarea name="description" class="textarea">{{ old('description', $category->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Update') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @push('js')
        <script src="{{ asset('assets/js/ckEditor5.js') }}"></script>
    @endpush
</x-admin::layout>
