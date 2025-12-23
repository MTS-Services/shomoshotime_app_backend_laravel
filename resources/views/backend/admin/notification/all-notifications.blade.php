<x-admin::layout>
    <x-slot name="title">All Notifications</x-slot>
    <x-slot name="breadcrumb">Notifications</x-slot>
    <x-slot name="page_slug">notifications</x-slot>

    <section>

        <div class="max-w-4xl mx-auto">
            <div class="glass-card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-black dark:text-white mb-2">
                            <i data-lucide="bell" class="w-8 h-8 inline-block mr-3 text-orange-500"></i>
                            Notifications
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            Manage all your notifications in one place
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-orange-500/20 rounded-xl">
                            <span class="text-orange-600 dark:text-orange-400 font-medium text-sm">
                                3 unread
                            </span>
                        </div>

                        <button
                            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-600 dark:text-blue-400 rounded-xl transition-colors duration-200 font-medium text-sm">
                            <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                            Mark All Read
                        </button>

                        <button
                            class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-600 dark:text-green-400 rounded-xl transition-colors duration-200 font-medium text-sm">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="glass-card rounded-2xl p-4 mb-6">
                <form class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</label>
                        <select class="px-3 py-2 bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option selected>All Notifications</option>
                            <option>Unread Only</option>
                            <option>Read Only</option>
                            <option>Private Messages</option>
                            <option>Public Announcements</option>
                        </select>

                        <select class="pl-3 pr-8 py-2 bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option selected>10</option>
                            <option>20</option>
                            <option>30</option>
                            <option>50</option>
                        </select>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing 1 to 5 of 5 notifications
                        <span class="ml-2 px-2 py-1 bg-orange-500/20 text-orange-600 dark:text-orange-400 rounded text-xs font-medium">
                            Unread Filter Active
                        </span>
                    </div>
                </form>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <div id="all-notifications-container" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Notification Item 1 -->
                    <div class="all-notification-item unread" data-notification-type="private">
                        <a href="{{route('notifications.details')}}" class="block p-6 hover:bg-white/30 dark:hover:bg-gray-800/30 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="relative flex-shrink-0">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg">
                                        <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                                    </div>
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-ping"></span>
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-black dark:text-white mb-1 line-clamp-1">
                                                New Private Message
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-2 line-clamp-2">
                                                You have a new private message from John Doe.
                                            </p>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 line-clamp-3">
                                                Click to view the message and reply if necessary.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-3">
                                        <div class="flex items-center gap-4">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                2 hours ago
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="lock" class="w-3 h-3"></i>
                                                Private
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Notification Item 2 -->
                    <div class="all-notification-item read" data-notification-type="public">
                        <a href="#" class="block p-6 hover:bg-white/30 dark:hover:bg-gray-800/30 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="relative flex-shrink-0">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg">
                                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-black dark:text-white mb-1 line-clamp-1">
                                                System Update
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-2 line-clamp-2">
                                                Our system will undergo maintenance tonight at 12 AM.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-3">
                                        <div class="flex items-center gap-4">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                1 day ago
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="users" class="w-3 h-3"></i>
                                                Public
                                            </span>
                                        </div>
                                        <span class="text-green-600 dark:text-green-400 font-medium">
                                            <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                                            Read
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Add more dummy items as needed -->
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 glass-card rounded-2xl p-6 text-center">
                <span class="text-gray-500 dark:text-gray-400">Page 1 of 1</span>
            </div>
        </div>

    </section>

    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    </script>
</x-admin::layout>
