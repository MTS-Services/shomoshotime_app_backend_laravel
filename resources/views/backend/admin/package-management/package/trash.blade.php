<x-admin::layout>
    <x-slot name="title">{{ __('Trashed Package List') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Trashed Package List') }}</x-slot>
    <x-slot name="page_slug">package</x-slot>
    <section>

        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Trashed Package List') }}</h2>
                <x-admin.primary-link href="{{ route('pam.package.index') }}">{{ __('Back') }} <i data-lucide="undo-2"
                        class="w-4 h-4"></i> </x-admin.primary-link>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <table class="table datatable table-zebra">
                <thead>
                    <tr>
                        <th width="5%">{{ __('SL') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Tag') }}</th>
                        <th>{{ __('Total Ad') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Deleted By') }}</th>
                        <th>{{ __('Deleted Date') }}</th>
                        <th width="10%">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </section>

    @push('js')
        <script src="{{ asset('assets/js/datatable.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let table_columns = [
                    ['name', true, true],
                    ['tag_label', true, true],
                    ['total_ad', true, true],
                    ['price', true, true],
                    ['status', true, true],
                    ['deleted_by', true, true],
                    ['deleted_at', true, true],
                    ['action', false, false],
                ];
                const details = {
                    table_columns: table_columns,
                    main_class: '.datatable',
                    displayLength: 10,
                    main_route: "{{ route('pam.package.trash') }}", // <-- your trash route for Category
                    order_route: "{{ route('update.sort.order') }}", // update if you want custom sort per category
                    export_columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    model: 'Package',
                };
                initializeDataTable(details);
            })
        </script>
    @endpush
</x-admin::layout>
