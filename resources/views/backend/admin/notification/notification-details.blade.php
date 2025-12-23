<x-admin::layout>
    <x-slot name="title">Notification Details</x-slot>
    <x-slot name="breadcrumb">Notifications / Details</x-slot>
    <x-slot name="page_slug">notification-details</x-slot>

    <section>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-slate-800 p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="glass-card rounded-2xl p-6 mb-8">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <a href="#"
                                class="p-2 hover:bg-gray-500/20 text-gray-600 dark:text-gray-400 rounded-lg transition-colors duration-200"
                                title="Back to notifications">
                                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                            </a>
                            <div>
                                <h1 class="text-3xl font-bold text-black dark:text-white mb-2">
                                    <i data-lucide="mail" class="w-8 h-8 inline-block mr-3 text-orange-500"></i>
                                    Notification Details
                                </h1>
                                <p class="text-gray-600 dark:text-gray-300">
                                    Private Message
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-orange-500/20 rounded-xl">
                                <span class="text-orange-600 dark:text-orange-400 font-medium text-sm">
                                    <i data-lucide="eye-off" class="w-4 h-4 inline mr-2"></i>
                                    Unread
                                </span>
                            </div>

                            <button
                                class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-600 dark:text-red-400 rounded-xl transition-colors duration-200 font-medium text-sm">
                                <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Content -->
                <div class="glass-card rounded-2xl overflow-hidden mb-8">
                    <div class="p-8">
                        <div class="flex items-start gap-6 mb-6">
                            <div class="relative flex-shrink-0">
                                <div
                                    class="w-16 h-16 rounded-xl flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg">
                                    <i data-lucide="mail" class="w-8 h-8 text-white"></i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-black dark:text-white mb-3">
                                    New Message from John Doe
                                </h2>

                                <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <span class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                        Aug 24, 2025 at 5:00 PM
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i data-lucide="lock" class="w-4 h-4"></i>
                                        Private
                                    </span>
                                    <span class="flex items-center gap-2 text-green-600 dark:text-green-400">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        Read 1 hour ago
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-lg dark:prose-invert max-w-none mb-6">
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border-l-4 border-orange-500">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-0">
                                    Hello, this is a test notification message to demonstrate the notification detail layout.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white/50 dark:bg-gray-800/30 rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold text-black dark:text-white mb-3">Additional Details</h3>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                This message contains some extra details for the admin to review. You can take action if necessary.
                            </p>
                        </div>

                        <div class="mt-6">
                            <a href="#"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors duration-200">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                View Related Content
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-black dark:text-white mb-4">
                        <i data-lucide="info" class="w-5 h-5 inline mr-2"></i>
                        Notification Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Notification ID
                            </label>
                            <p class="text-black dark:text-white font-mono text-sm">
                                123456
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Type
                            </label>
                            <p class="text-black dark:text-white">
                                Private Message
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Received At
                            </label>
                            <p class="text-black dark:text-white">
                                Aug 24, 2025 at 5:00:00 PM
                                <span class="text-gray-500 dark:text-gray-400 text-sm">(2 hours ago)</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Read At
                            </label>
                            <p class="text-black dark:text-white">
                                Aug 24, 2025 at 6:00:00 PM
                                <span class="text-gray-500 dark:text-gray-400 text-sm">(1 hour ago)</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-8 text-center">
                    <a href="#"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gray-500/20 hover:bg-gray-500/30 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors duration-200">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Back to All Notifications
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    </script>
</x-admin::layout>
