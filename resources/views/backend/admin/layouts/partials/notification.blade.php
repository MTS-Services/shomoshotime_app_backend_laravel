<!-- Notifications Panel -->
<div x-show="showNotifications" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="hidden fixed right-0 top-0 h-full max-h-screen z-50 py-2 pr-2 backdrop-blur-sm"
    :class="showNotifications ? '!block' : '!hidden'">

    <div
        class="w-96 glass-card overflow-y-auto custom-scrollbar rounded-2xl h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl dark:shadow-gray-900/50">

        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h3>
            <button @click="showNotifications = false"
                class="p-2 rounded-lg bg-orange-500/20 transition-colors inset-shadow-2xs hover:bg-orange-500/10 group cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-x w-5 h-5 text-orange-800 dark:text-orange-100 group-hover:text-orange-500">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <!-- Notifications List -->
        <div class="flex-1 space-y-4 px-6 py-4 overflow-y-auto">

            <!-- Dummy Notification 1 -->
            <div
                class="bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-4 transition">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange-500/20 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <line x1="12" x2="12" y1="2" y2="22"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-orange-500 rounded-full animate-ping"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white text-sm font-medium mb-1">Payment Successful</p>
                        <p class="text-gray-600 dark:text-gray-300 text-xs">User John Doe has successfully made a
                            payment.</p>
                        <span class="text-gray-400 dark:text-gray-400 text-xs">1 hour ago</span>
                    </div>
                </div>
            </div>

            <!-- Dummy Notification 2 -->
            <div
                class="bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-4 transition">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-green-500/20 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M20 6 9 17l-5-5"></path>
                        </svg>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white text-sm font-medium mb-1">Plan Assigned</p>
                        <p class="text-gray-600 dark:text-gray-300 text-xs">Plan assigned successfully to Jane Smith.
                        </p>
                        <span class="text-gray-400 dark:text-gray-400 text-xs">3 hours ago</span>
                    </div>
                </div>
            </div>

            <!-- Dummy Notification 3 -->
            <div
                class="bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-4 transition">
                <a href="{{route('notifications.index')}}">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500/20 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 dark:text-white text-sm font-medium mb-1">New Message</p>
                            <p class="text-gray-600 dark:text-gray-300 text-xs">You have received a new message from
                                Admin.</p>
                            <span class="text-gray-400 dark:text-gray-400 text-xs">5 hours ago</span>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Footer buttons -->
        <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700">
            <a href="#"
                class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Mark All As Read
            </a>
            <a href="#" class="p-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors">
                See All
            </a>
        </div>
    </div>
</div>
