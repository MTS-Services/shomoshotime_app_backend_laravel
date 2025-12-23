<x-admin::layout>
    <x-slot name="title">{{ __('Conversation List') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Conversation List') }}</x-slot>
    <x-slot name="page_slug">conversation</x-slot>



    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Conversation List') }}
                </h2>
                <div class="flex items-center gap-2">
                    <x-admin.primary-link secondary="true" href="{{ route('cm.conversation.trash') }}">
                        {{ __('Trash') }} <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </x-admin.primary-link>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <table class="table datatable table-zebra">
                <thead>
                    <tr>
                        <th width="5%">{{ __('SL') }}</th>
                        <th>{{ __('Participants') }}</th>
                        <th>{{ __('Type') }}</th>   
                        <th>{{ __('Last Message At') }}</th>
                        <th>{{ __('Created By') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th width="10%">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>

    {{-- Details Modal --}}
    <x-admin.details-modal />

    @push('js')
        <script src="{{ asset('assets/js/datatable.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let table_columns = [

                    ['conversation_details', false, false],
                    ['type', true, true],
                    ['last_message_at', true, true],
                    ['created_by', true, true],
                    ['created_at', true, true],
                    ['action', false, false],
                ];

                const details = {
                    table_columns: table_columns,
                    main_class: '.datatable',
                    displayLength: 10,
                    main_route: "{{ route('cm.conversation.index') }}",
                    order_route: "{{ route('update.sort.order') }}",
                    export_columns: [0, 1, 2, 3, 4],
                    model: 'Conversation',
                };

                initializeDataTable(details);
            });
        </script>

        {{-- Details Modal --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                $(document).on('click', '.view', function() {
                    const id = $(this).data('id');
                    const route = "{{ route('cm.conversation.show', ':id') }}".replace(':id', id);

                    const details = [{
                            label: '{{ __('Name') }}',
                            key: 'name',
                        },
                        {
                            label: '{{ __('Type') }}',
                            key: 'type',
                        },
                        {
                            label: '{{ __('Last Message At') }}',
                            key: 'last_message_at',
                        },
                    ];

                    showDetailsModal(route, id, '{{ __('Conversation Details') }}', details);
                });
            });
        </script>
    @endpush
</x-admin::layout>
