<x-admin::layout>
    <x-slot name="title">{{ __('General Settings') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('General Settings') }}</x-slot>
    <x-slot name="page_slug">app-general-settings</x-slot>
    <section>
        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-1  {{ isset($documentation) && $documentation ? 'md:grid-cols-7' : '' }}">
            <div class="glass-card rounded-2xl p-6 md:col-span-5">
                <form action="{{ route('app-settings.update-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 2xl:grid-cols-9 gap-5">
                        <div class="2xl:col-span-6 grid grid-cols-1 gap-5 sm:grid-cols-2 h-fit">
                            <div class="space-y-2 ">
                                <p class="label">{{ __('App Name') }}</p>
                                <label class="input flex items-center px-2">
                                    <input type="text" placeholder="App Name"
                                        value="{{ $general_settings['app_name'] ?? '' }}" name="app_name"
                                        class="flex-1" />
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('app_name')" />
                            </div>

                            <div class="space-y-2">
                                <p class="label">{{ __('App Short Name') }}</p>
                                <label class="input flex items-center px-2">
                                    <input type="text" placeholder="App Short Name"
                                        value="{{ $general_settings['short_name'] ?? '' }}" name="short_name"
                                        class="flex-1" />
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('short_name')" />
                            </div>


                            <div class="space-y-2">
                                <p class="label">{{ __('Timezone') }}</p>
                                <select name="timezone" class="select select2">
                                    <option value="" selected hidden>{{ __('Select timezone') }}</option>
                                    @foreach ($timezones as $timezone)
                                        <option value="{{ $timezone['timezone'] }}"
                                            @if (isset($general_settings['timezone']) && $general_settings['timezone'] == $timezone['timezone']) selected @endif>
                                            {{ $timezone['name'] ?? '' }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                            </div>
                            <div class="space-y-2">
                                <p class="label">{{ __('Environment') }}
                                    <small>({{ __('Best to keep in production') }})</small>
                                </p>
                                <select name="environment" class="select">
                                    @foreach (App\Models\ApplicationSetting::getEnvironmentInfos() as $key => $info)
                                        <option value="{{ $key }}"
                                            @if (isset($general_settings['environment']) && $general_settings['environment'] == $key) selected @endif>{{ $info }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('environment')" />
                            </div>
                            <div class="space-y-2">
                                <p class="label">{{ __('App Debug') }}
                                    <small>({{ __('Best to keep in false') }})</small>
                                </p>
                                <select name="app_debug" class="select">
                                    @foreach (App\Models\ApplicationSetting::getAppDebugInfos() as $key => $info)
                                        <option value="{{ $key }}"
                                            @if (isset($general_settings['app_debug']) && $general_settings['app_debug'] == $key) selected @endif>{{ $info }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('app_debug')" />
                            </div>
                            <div class="space-y-2">
                                <p class="label">{{ __('Enable Debugbar') }}
                                    <small>({{ __('Best to keep in false') }})</small>
                                </p>
                                <select name="debugbar" class="select">
                                    @foreach (App\Models\ApplicationSetting::getDebugbarInfos() as $key => $info)
                                        <option value="{{ $key }}"
                                            @if (isset($general_settings['debugbar']) && $general_settings['debugbar'] == $key) selected @endif>{{ $info }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('debugbar')" />
                            </div>
                            <div class="space-y-2 sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <div class="space-y-2">
                                    <p class="label">{{ __('Date Format') }}</p>
                                    <select name="date_format" class="select">
                                        @foreach (App\Models\ApplicationSetting::getDateFormatInfos() as $key => $info)
                                            <option value="{{ $key }}"
                                                @if (isset($general_settings['date_format']) && $general_settings['date_format'] == $key) selected @endif>
                                                {{ $info }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_format')" />
                                </div>
                                <div class="space-y-2">
                                    <p class="label">{{ __('Time Format') }}</p>
                                    <select name="time_format" class="select">
                                        @foreach (App\Models\ApplicationSetting::getTimeFormatInfos() as $key => $info)
                                            <option value="{{ $key }}"
                                                @if (isset($general_settings['time_format']) && $general_settings['time_format'] == $key) selected @endif>
                                                {{ $info }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('time_format')" />
                                </div>
                                <div class="space-y-2">
                                    <p class="label">{{ __('Default Theme Mode') }}</p>
                                    <select name="theme_mode" class="select">
                                        @foreach (App\Models\ApplicationSetting::getThemeModeInfos() as $key => $info)
                                            <option value="{{ $key }}"
                                                @if (isset($general_settings['theme_mode']) && $general_settings['theme_mode'] == $key) selected @endif>
                                                {{ $info }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('theme_mode')" />
                                </div>
                            </div>
                        </div>
                        <div class="2xl:col-span-3 grid grid-cols-1 gap-5 h-fit">
                            <div class="space-y-2">
                                <p class="label">{{ __('App Logo') }}<small>({{ __('Max: 400x400') }})</small></p>
                                <input type="file" name="app_logo" class="filepond" id="app_logo"
                                    accept="image/jpeg, image/png, image/jpg, image/webp, image/svg">
                                <x-input-error class="mt-2" :messages="$errors->get('app_logo')" />
                            </div>
                            <div class="space-y-2">
                                <p class="label">{{ __('Favicon') }} <small>({{ __('16x16') }})</small></p>
                                <input type="file" name="favicon" class="filepond" id="favicon"
                                    accept="image/jpeg, image/png, image/jpg, image/webp, image/svg">
                                <x-input-error class="mt-2" :messages="$errors->get('favicon')" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <x-admin.primary-button>{{ __('Save') }}</x-admin.primary-button>
                    </div>
                </form>
            </div>

        </div>
    </section>

    @push('js')
        <script src="{{ asset('assets/js/filepond.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                file_upload(["#favicon"], ["image/jpeg", "image/png", "image/jpg, image/webp, image/svg"], {
                    "#favicon": "{{ isset($general_settings['favicon']) ? asset('storage/' . $general_settings['favicon']) : null }}"
                });
                file_upload(["#app_logo"], ["image/jpeg", "image/png", "image/jpg, image/webp, image/svg"], {
                    "#app_logo": "{{ isset($general_settings['app_logo']) ? asset('storage/' . $general_settings['app_logo']) : null }}"
                });
            });
        </script>
    @endpush
</x-admin::layout>
