<x-admin::layout>
    <x-slot name="title">{{ isset($profile) ? __('Update Profile') : __('Create Profile') }}</x-slot>
    <x-slot name="breadcrumb">{{ isset($profile) ? __('Update Profile') : __('Create Profile') }}</x-slot>
    <x-slot name="page_slug">profile</x-slot>
    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">
                    {{ isset($profile) ? __('Update Profile') : __('Create Profile') }}
                </h2>
                <x-admin.primary-link href="{{ route('company-info') }}">{{ __('Company Information') }}
                </x-admin.primary-link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-1">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('user-profiles.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        {{-- Date of Birth --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('Date of Birth') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="date" name="dob" value="{{ old('dob', $profile->dob ?? '') }}"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('dob')" />
                        </div>

                        {{-- Gender --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('Gender') }}</p>
                            <label class="input flex items-center px-2">
                                <select name="gender" class="flex-1">
                                    @foreach (\App\Models\UserProfile::genderList() as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('gender', $profile->gender ?? \App\Models\UserProfile::GENDER_OTHER) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        {{-- City (EN) --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('City') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}"
                                    class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>


                        {{-- Country (EN) --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('Country') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" name="country"
                                    value="{{ old('country', $profile->country ?? 'Kuwait') }}" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('country')" />
                        </div>


                        {{-- Postal Code --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('Postal Code') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="text" name="postal_code"
                                    value="{{ old('postal_code', $profile->postal_code ?? '') }}" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>
                        {{-- Website --}}
                        <div class="space-y-2">
                            <p class="label">{{ __('Website') }}</p>
                            <label class="input flex items-center px-2">
                                <input type="url" name="website"
                                    value="{{ old('website', $profile->website ?? '') }}" class="flex-1" />
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('website')" />
                        </div>
                        {{-- social media --}}
                        <div id="media-fields-container" class="space-y-4 w-full col-span-2">
                            {{-- Initial Field --}}
                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 media-input-group bg-white p-4 rounded-xl shadow">

                                {{-- Media Name --}}
                                <div class="w-full">
                                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('Media Name') }}</p>
                                    <input type="text" name="medianames[]"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        value="{{ old('medianames.0', $profile->social_links['media'][0]['name'] ?? '') }}">
                                    <x-input-error class="mt-1 text-sm text-red-500" :messages="$errors->get('medianames.0')" />
                                </div>

                                {{-- Media Link --}}
                                <div class="w-full">
                                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('Media Link') }}</p>
                                    <input type="url" name="medialinks[]"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        value="{{ old('medialinks.0', $profile->social_links['media'][0]['link'] ?? '') }}">
                                    <x-input-error class="mt-1 text-sm text-red-500" :messages="$errors->get('medialinks.0')" />
                                </div>

                                {{-- Empty space for remove button --}}


                                {{-- Add More Button --}}
                                <div class="w-full flex items-end">
                                    <button type="button" id="add-media-field"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-blue-500 to-green-500 hover:from-blue-600 hover:to-green-600 text-white text-sm font-medium rounded-md">
                                        + {{ __('Add More Media') }}
                                    </button>

                                </div>

                            </div>
                        </div>




                        {{-- Bio (EN) --}}
                        <div class="space-y-2 sm:col-span-2">
                            <p class="label">{{ __('Bio)') }}</p>
                            <textarea name="bio" class="textarea">{{ old('bio', $profile->bio_en ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                        </div>

                    </div>

                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>
                            {{ isset($profile) ? __('Update') : __('Create') }}
                        </x-admin.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('js')
        <script src="{{ asset('assets/js/ckEditor5.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addMediaButton = document.getElementById('add-media-field');
                const mediaContainer = document.getElementById('media-fields-container');
                let mediaIndex = {{ count($profile->social_links['media'] ?? []) }};

                addMediaButton.addEventListener('click', function() {
                    const newFieldRow = document.createElement('div');
                    newFieldRow.className =
                        'grid grid-cols-1 md:grid-cols-3 gap-4 media-row bg-white p-4 rounded-xl shadow';

                    newFieldRow.innerHTML = `
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('Media Name') }}</p>
                    <input type="text" name="medianames[${mediaIndex}]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('Media Link') }}</p>
                    <input type="url" name="medialinks[${mediaIndex}]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-media-field px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                        {{ __('Remove') }}
                    </button>
                </div>
            `;

                    mediaContainer.appendChild(newFieldRow);

                    newFieldRow.querySelector('.remove-media-field').addEventListener('click', function() {
                        newFieldRow.remove();
                    });

                    mediaIndex++;
                });
            });
        </script>
    @endpush
</x-admin::layout>
