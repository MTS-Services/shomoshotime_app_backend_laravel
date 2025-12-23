<x-admin::layout>
    <x-slot name="title">{{ __('Trashed Conversation List') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Trashed Conversation List') }}</x-slot>
    <x-slot name="page_slug">conversation</x-slot>

    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">
                    {{ __('Trashed Conversation List') }}</h2>
                <x-admin.primary-link href="{{ route('cm.conversation.index') }}">
                    {{ __('Back') }}
                    <i data-lucide="undo-2" class="w-4 h-4"></i>
                </x-admin.primary-link>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <table class="table datatable table-zebra">
                <thead>
                    <tr>
                        <th width="5%">{{ __('SL') }}</th>
                          <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Last Message At') }}</th>
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
                    ['type', true, true],
                    ['last_message_at', true, true],
                    ['deleted_by', true, true],
                    ['deleted_at', true, true],
                    ['action', false, false],
                ];
                const details = {
                    table_columns: table_columns,
                    main_class: '.datatable',
                    displayLength: 10,
                    main_route: "{{ route('cm.conversation.trash') }}",
                    order_route: "{{ route('update.sort.order') }}",
                    export_columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    model: 'Conversation',
                };
                initializeDataTable(details);
            });
        </script>
    @endpush
</x-admin::layout>
