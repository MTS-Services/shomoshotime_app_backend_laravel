<x-admin::layout>
    <x-slot name="title">{{ __('Create Property') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Create Property') }}</x-slot>
    <x-slot name="page_slug">property</x-slot>
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Create Property') }}</h2>
                <x-admin.primary-link href="{{ route('pm.property.index') }}">
                    {{ __('Back') }}
                    <i data-lucide="undo-2" class="w-4 h-4"></i>
                </x-admin.primary-link>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1 {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('pm.property.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <!-- Title -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Title') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" placeholder="Title" value="{{ old('title') }}" name="title"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <!-- Property Type -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Property Type') }}</p>
                            <label class="input flex items-center px-2">
                                <select name="property_type_id" class="flex-1">
                                    <option value="">{{ __('Select Property Type') }}</option>
                                    @foreach ($propertyTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('property_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('property_type_id')" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Category') }}</p>
                            <label class="input flex items-center px-2">
                                <select name="category_id" class="flex-1">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <!-- Area -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Area') }}</p>
                            <label class="input flex items-center px-2">
                                <select name="area_id" class="flex-1">
                                    <option value="">{{ __('Select Area') }}</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('area_id')" />
                        </div>


                        <!-- Price -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Price') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="number" placeholder="Price" value="{{ old('price') }}" name="price"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Expiration Date -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Expires At') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('expires_at')" />
                        </div>

                        <!-- Renewal Date -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Renew At') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="date" name="renew_at" value="{{ old('renew_at') }}" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('renew_at')" />
                        </div>
                        {{-- description --}}
                        <div class="space-y-2 sm:col-span-2">
                            <p class="label">{{ __('Description') }}</p>
                            <textarea name="description" class="textarea">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        {{-- Primary Image --}}
                        <div class="space-y-2 sm:col-span-2">
                            <label class="label">{{ __('Primary File') }} <span class="text-danger">*</span></label>
                            <input type="file" name="file"
                                accept="image/jpeg, image/png, image/jpg, image/webp, image/svg, video/mp4"
                                class="filepond" id="file">
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        </div>
                        {{-- images --}}
                        <div class="space-y-2 sm:col-span-2">
                            <label class="label" for="files">{{ __('Gallery Files') }}</label>
                            <input type="file" name="files[]"
                                accept="image/jpeg, image/png, image/jpg, image/webp, image/svg, video/mp4"
                                class="filepond" multiple id="files">
                            <x-input-error class="mt-2" :messages="$errors->get('files.*')" />
                            <x-input-error class="mt-2" :messages="$errors->get('files')" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Create') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>
            {{-- documentation will be loaded here and add md:col-span-2 class --}}
        </div>
        @push('js')
            <script src="{{ asset('assets/js/ckEditor5.js') }}"></script>
            <script src="{{ asset('assets/js/filepond.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    file_upload(["#file"], ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg'],
                        []);
                    file_upload(["#files"], ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg'],
                        [], true);
                });
            </script>
        @endpush

    </section>
</x-admin::layout>
