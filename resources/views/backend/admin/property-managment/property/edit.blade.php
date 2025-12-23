<x-admin::layout>
    <x-slot name="title">{{ __('Edit Property') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Edit Property') }}</x-slot>
    <x-slot name="page_slug">property</x-slot>
    {{-- @dd($property) --}}
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Edit Property') }}</h2>
                <x-admin.primary-link href="{{ route('pm.property.index') }}">
                    {{ __('Back') }}
                    <i data-lucide="undo-2" class="w-4 h-4"></i>
                </x-admin.primary-link>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1 {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('pm.property.update', encrypt($property->id)) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <!-- Title -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Title') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" placeholder="Title" value="{{ old('title', $property->title) }}"
                                    name="title" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>


                        <!-- Property Type -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Property Type') }}</p>
                            <label class="input flex items-center px-2">
                                <select name="property_type_id" class="flex-1">
                                    <option value="">{{ __('Select Property Type') }}</option>
                                    @foreach ($property_types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('property_type_id', $property->property_type_id) == $type->id ? 'selected' : '' }}>
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
                                            {{ old('category_id', $property->category_id) == $category->id ? 'selected' : '' }}>
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
                                            {{ old('area_id', $property->area_id) == $area->id ? 'selected' : '' }}>
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
                                <input type="number" placeholder="Price" value="{{ old('price', $property->price) }}"
                                    name="price" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Expiration Date -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Expires At') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="date" name="expires_at"
                                    value="{{ old('expires_at', \Carbon\Carbon::parse($property->expires_at)->format('Y-m-d')) }}"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('expires_at')" />
                        </div>

                        <!-- Renewal Date -->
                        <div class="space-y-2">
                            <p class="label">{{ __('Renew At') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="date" name="renew_at"
                                    value="{{ old('renew_at', \Carbon\Carbon::parse($property->renew_at)->format('Y-m-d')) }}"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('renew_at')" />
                        </div>
                        {{-- Description --}}
                        <div class="space-y-2 sm:col-span-2">
                            <p class="label">{{ __('Description') }}</p>
                            <textarea name="description" class="textarea">{{ old('description', $property->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                        {{-- Primary File --}}
                        <div class="space-y-2 sm:col-span-2">
                            <label class="label">{{ __('Primary File') }} <span class="text-danger">*</span></label>
                            <input type="file" name="file"
                                accept="image/jpeg, image/png, image/jpg, image/webp, image/svg, video/mp4"
                                class="filepond" id="file">
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        </div>

                        {{-- Gallery Files --}}
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
                        <x-admin.primary-button>{{ __('Update') }}</x-admin.primary-button>
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
                    const existingFiles = {
                        "#file": "{{ $property?->primaryImage?->modified_image }}",
                    };

                    const existingMultiFiles = {
                        "#files": @json($property->nonPrimaryImages->map(fn($img) => $img->modified_image)->toArray()),
                    };

                    file_upload(["#file"], ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml',
                        'video/mp4'
                    ], existingFiles);

                    file_upload(["#files"], ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml',
                        'video/mp4'
                    ], existingMultiFiles, true);
                });
            </script>
        @endpush
    </section>
</x-admin::layout>
