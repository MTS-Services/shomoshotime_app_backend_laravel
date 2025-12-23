<x-admin::layout>
    <x-slot name="title">{{ __('Participant List') }}</x-slot>
    <x-slot name="breadcrumb">{{ __('Participant List') }}</x-slot>
    <x-slot name="page_slug">participant</x-slot>

    <section>
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-text-black dark:text-text-white">{{ __('Participant List') }}</h2>
                <div class="flex items-center gap-2">
                    <x-admin.primary-link secondary="true" href="{{ route('cm.participant.trash') }}">
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
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Conversation ID') }}</th>
                        <th>{{ __('Joined At') }}</th>
                        <th>{{ __('Created By') }}</th>
                        <th>{{ __('Created Date') }}</th>
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
                    ['user_id', true, true],
                    // ['last_read_message_id', true, true],
                    ['conversation_id', true, true],
                    ['joined_at', true, true],
                    ['created_by', true, true],
                    ['created_at', true, true],
                    ['action', false, false],
                ];

                const details = {
                    table_columns: table_columns,
                    main_class: '.datatable',
                    displayLength: 10,
                    main_route: "{{ route('cm.participant.index') }}",
                    order_route: "{{ route('update.sort.order') }}",
                    export_columns: [0, 1, 2, 3, 4],
                    model: 'Participant',
                };

                initializeDataTable(details);
            });
        </script>

        {{-- Details Modal --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                $(document).on('click', '.view', function() {
                    const id = $(this).data('id');
                    const route = "{{ route('cm.participant.show', ':id') }}".replace(':id', id);

                    const details = [{
                            label: '{{ __('User') }}',
                            key: 'user_name',
                        },
                        {
                            label: '{{ __('Conversation') }}',
                            key: 'conversation_name',
                        },
                        {
                            label: '{{ __('Joined At') }}',
                            key: 'joined_at',
                        },
                        {
                            label: '{{ __('Is Muted') }}',
                            key: 'is_muted',
                        },
                        {
                            label: '{{ __('Message ID') }}',
                            key: 'message_name',
                        },

                    ];

                    showDetailsModal(route, id, '{{ __('Participant Details') }}', details);
                });
            });
        </script>
    @endpush
</x-admin::layout>
