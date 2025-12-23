@props([
    'icon' => 'folder', // Default parent icon
    'name' => 'Multi Navlink',
    'boxicon' => false,
    'active' => '',
    'page_slug' => '',
    'items' => [], // Array of nav items (single, dropdown, or multi-dropdown)
    'type' => 'dropdown', // 'dropdown' or 'single' - determines if main item is clickable
    'route' => '', // Route for main item when type is 'single'
    'direction' => 'ltr', // 'ltr' or 'rtl' - New prop for direction
])

@php
    // Default icons for different levels
    $defaultParentIcon = $icon ?: 'folder';
    $defaultSubitemIcon = 'tags';
    $defaultMultiSubitemIcon = 'circle';

    // Check if main item or any sub-item is active
    $isMainActive = $type === 'single' && $page_slug == $active;
    $isDropdownActive = false;

    foreach ($items as $item) {
        if (isset($item['active']) && $page_slug == $item['active']) {
            $isDropdownActive = true;
            break;
        }
        // Check nested items for multi-dropdown
        if (isset($item['subitems'])) {
            foreach ($item['subitems'] as $subitem) {
                if (isset($subitem['active']) && $page_slug == $subitem['active']) {
                    $isDropdownActive = true;
                    break 2;
                }
            }
        }
    }

    $isAnyActive = $isMainActive || $isDropdownActive;
@endphp

<div x-data="{
    open: {{ $isDropdownActive ? 'true' : 'false' }},
    collapsedDropdown: false,
    init() {
        // Auto expand if any dropdown item is active
        if ({{ $isDropdownActive ? 'true' : 'false' }}) {
            this.open = true;
        }
    },
    toggleCollapsedDropdown() {
        if (!desktop || !sidebar_expanded) {
            this.collapsedDropdown = !this.collapsedDropdown;
            // Close other collapsed dropdowns by dispatching event
            $dispatch('close-collapsed-dropdowns', { except: $el });
        }
    },
    closeCollapsedDropdown() {
        this.collapsedDropdown = false;
    }
}"
    @close-collapsed-dropdowns.window="if ($event.detail.except !== $el) { closeCollapsedDropdown() }"
    @click.away="closeCollapsedDropdown()">

    @if ($type === 'single')
        <a href="{{ $route }}"
            class="sidebar-item flex items-center gap-4 p-3 rounded-xl hover:bg-bg-black/10 dark:hover:bg-bg-white/10 text-text-white transition-all duration-200 group {{ $isMainActive ? 'active' : '' }}">
            <div
                class="w-8 h-8 bg-bg-black/10 dark:bg-bg-white/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform relative">
                @if ($boxicon)
                    <i class="{{ $defaultParentIcon }} text-text-black dark:text-text-white"></i>
                @else
                    <i data-lucide="{{ $defaultParentIcon }}"
                        class="w-5 h-5 stroke-bg-black dark:stroke-bg-white flex-shrink-0"></i>
                @endif
                {{-- Active indicator for single item, positioned based on direction --}}
                <div x-show="!((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) && {{ $isAnyActive ? 'true' : 'false' }}"
                    class="absolute -top-1 {{ $direction === 'ltr' ? '-right-1' : '-left-1' }} w-3 h-3 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse invisible"
                    :class="{
                        'visible': !((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) &&
                            {{ $isAnyActive ? 'true' : 'false' }}
                    }">
                </div>
            </div>
            <span x-show="(desktop && sidebar_expanded) || (!desktop && mobile_menu_open)"
                x-transition:enter="transition-all duration-300 delay-75"
                x-transition:enter-start="opacity-0 {{ $direction === 'ltr' ? 'translate-x-4' : '-translate-x-4' }}"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 {{ $direction === 'ltr' ? '-translate-x-4' : 'translate-x-4' }}"
                class="font-medium {{ $isMainActive ? 'text-text-black dark:text-text-white' : 'text-text-light-secondary dark:text-text-dark-primary' }}">{{ __($name) }}</span>
            <div x-show="(desktop && sidebar_expanded) || (!desktop && mobile_menu_open)"
                class="{{ $direction === 'ltr' ? 'ml-auto' : 'mr-auto' }} {{ $isMainActive ? 'block' : 'hidden' }}">
                <div class="w-2 h-2 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse"></div>
            </div>
        </a>
    @else
        <button
            @click="((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) ? (open = !open) : toggleCollapsedDropdown()"
            class="sidebar-item flex items-center gap-4 p-3 rounded-xl hover:bg-bg-black/10 dark:hover:bg-bg-white/10 text-text-white transition-all duration-200 group w-full {{ $isAnyActive ? 'active' : '' }}">
            <div
                class="w-8 h-8 bg-bg-black/10 dark:bg-bg-white/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform relative">
                @if ($boxicon)
                    <i class="{{ $defaultParentIcon }} text-blue"></i>
                @else
                    <i data-lucide="{{ $defaultParentIcon }}"
                        class="w-5 h-5 stroke-bg-black dark:stroke-bg-white flex-shrink-0"></i>
                @endif

                {{-- Active indicator for dropdown, positioned based on direction --}}
                <div x-show="!((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) && {{ $isAnyActive ? 'true' : 'false' }}"
                    class="absolute -top-1 {{ $direction === 'ltr' ? '-right-1' : '-left-1' }} w-3 h-3 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse invisible"
                    :class="{
                        'visible': !((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) &&
                            {{ $isAnyActive ? 'true' : 'false' }}
                    }">
                </div>
            </div>

            <span x-show="(desktop && sidebar_expanded) || (!desktop && mobile_menu_open)"
                x-transition:enter="transition-all duration-300 delay-75"
                x-transition:enter-start="opacity-0 {{ $direction === 'ltr' ? 'translate-x-4' : '-translate-x-4' }}"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 {{ $direction === 'ltr' ? '-translate-x-4' : 'translate-x-4' }}"
                class="font-medium {{ $direction === 'ltr' ? 'text-left' : 'text-right' }} {{ $isAnyActive ? 'text-text-black dark:text-text-white' : 'text-text-light-secondary dark:text-text-dark-primary' }}">{{ __($name) }}</span>

            <div x-show="(desktop && sidebar_expanded) || (!desktop && mobile_menu_open)"
                class="{{ $direction === 'ltr' ? 'ml-auto' : 'mr-auto' }} transition-transform duration-200"
                :class="open ? 'rotate-180' : ''">
                <i data-lucide="chevron-down" class="w-4 h-4 stroke-bg-black dark:stroke-bg-white"></i>
            </div>
        </button>
    @endif

    @if ($type === 'dropdown' && count($items) > 0)
        {{-- Collapsed Dropdown (for compact sidebar) --}}
        <div x-show="collapsedDropdown && !((desktop && sidebar_expanded) || (!desktop && mobile_menu_open))"
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 {{ $direction === 'ltr' ? 'translate-x-2' : '-translate-x-2' }} scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 {{ $direction === 'ltr' ? 'translate-x-2' : '-translate-x-2' }} scale-95"
            class="hidden absolute z-[9999] min-w-64 max-w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 py-3 {{ $direction === 'ltr' ? 'left-full ml-2' : 'right-full mr-2' }} top-0"
            :class="(collapsedDropdown && !((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) ? '!block' :
                '!hidden')"
            style="backdrop-filter: blur(12px);" x-init="$nextTick(() => {
                if (collapsedDropdown) {
                    const triggerRect = $el.previousElementSibling.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    const dropdownWidth = $el.offsetWidth;
                    const dropdownHeight = $el.offsetHeight; // Get actual height
            
                    let targetX = triggerRect.{{ $direction === 'ltr' ? 'right' : 'left' }} + 8;
                    let targetY = triggerRect.top + (triggerRect.height / 2) - (dropdownHeight / 2);
            
                    // Adjust X position if it goes off screen
                    if ('{{ $direction }}' === 'ltr') {
                        if (targetX + dropdownWidth > viewportWidth - 20) {
                            targetX = triggerRect.left - dropdownWidth - 8; // Position to the left
                        }
                    } else { // rtl
                        if (targetX - dropdownWidth < 20) {
                            targetX = triggerRect.right + 8; // Position to the right
                        }
                    }
            
                    // Adjust Y position if it goes off screen
                    if (targetY < 20) {
                        targetY = 20;
                    } else if (targetY + dropdownHeight > viewportHeight - 20) {
                        targetY = viewportHeight - dropdownHeight - 20;
                    }
            
                    $el.style.{{ $direction === 'ltr' ? 'left' : 'right' }} = targetX + 'px';
                    $el.style.top = targetY + 'px';
                }
            })"
            x-effect="
                if (collapsedDropdown) {
                    $nextTick(() => {
                        const triggerRect = $el.previousElementSibling.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        const dropdownWidth = $el.offsetWidth;
                        const dropdownHeight = $el.offsetHeight;
                        
                        let targetX = triggerRect.{{ $direction === 'ltr' ? 'right' : 'left' }} + 8;
                        let targetY = triggerRect.top + (triggerRect.height / 2) - (dropdownHeight / 2);

                        if ('{{ $direction }}' === 'ltr') {
                            if (targetX + dropdownWidth > viewportWidth - 20) {
                                targetX = triggerRect.left - dropdownWidth - 8;
                            }
                        } else {
                            if (targetX - dropdownWidth < 20) {
                                targetX = triggerRect.right + 8;
                            }
                        }

                        if (targetY < 20) {
                            targetY = 20;
                        } else if (targetY + dropdownHeight > viewportHeight - 20) {
                            targetY = viewportHeight - dropdownHeight - 20;
                        }

                        $el.style.{{ $direction === 'ltr' ? 'left' : 'right' }} = targetX + 'px';
                        $el.style.top = targetY + 'px';
                    });
                }">

            <div class="px-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center">
                        @if ($boxicon)
                            <i class="{{ $defaultParentIcon }} text-violet-600 dark:text-violet-400"></i>
                        @else
                            <i data-lucide="{{ $defaultParentIcon }}"
                                class="w-5 h-5 stroke-violet-600 dark:stroke-violet-400"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __($name) }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($items) }} items</p>
                    </div>
                </div>
            </div>

            <div class="px-2 py-2 max-h-96 overflow-y-auto custom-scrollbar">
                @foreach ($items as $item)
                    @php
                        $subitemIcon = $item['icon'] ?? $defaultSubitemIcon;
                        $subitemBoxicon = $item['boxicon'] ?? false;
                    @endphp

                    @if (isset($item['type']) && $item['type'] === 'single')
                        <a href="{{ $item['route'] }}"
                            class="flex items-center gap-3 p-3 {{ $direction === 'ltr' ? 'mx-2' : 'mx-2' }} rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all duration-200 group {{ isset($item['active']) && $page_slug == $item['active'] ? 'bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800' : '' }}">
                            <div
                                class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform">
                                @if ($subitemBoxicon)
                                    <i
                                        class="{{ $subitemIcon }} text-sm {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-gray-600 dark:text-gray-400' }}"></i>
                                @else
                                    <i data-lucide="{{ $subitemIcon }}"
                                        class="w-4 h-4 {{ isset($item['active']) && $page_slug == $item['active'] ? 'stroke-violet-600 dark:stroke-violet-400' : 'stroke-gray-600 dark:stroke-gray-400' }}"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <span
                                    class="font-medium text-sm {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300' }}">{{ __($item['name']) }}</span>
                            </div>
                            @if (isset($item['active']) && $page_slug == $item['active'])
                                <div class="w-2 h-2 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse"></div>
                            @endif
                        </a>
                    @elseif (isset($item['subitems']) && count($item['subitems']) > 0)
                        <div x-data="{ subOpen: {{ (function () use ($item, $page_slug) {
                            foreach ($item['subitems'] as $subitem) {
                                if (isset($subitem['active']) && $page_slug == $subitem['active']) {
                                    return 'true';
                                }
                            }
                            return 'false';
                        })() }} }" class="{{ $direction === 'ltr' ? 'mx-2' : 'mx-2' }}">

                            <button @click="subOpen = !subOpen"
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all duration-200 w-full group">
                                <div
                                    class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform">
                                    @if ($subitemBoxicon)
                                        <i class="{{ $subitemIcon }} text-sm text-gray-600 dark:text-gray-400"></i>
                                    @else
                                        <i data-lucide="{{ $subitemIcon }}"
                                            class="w-4 h-4 stroke-gray-600 dark:stroke-gray-400"></i>
                                    @endif
                                </div>
                                <span
                                    class="font-medium text-sm text-gray-700 dark:text-gray-300 flex-1 {{ $direction === 'ltr' ? 'text-left' : 'text-right' }}">{{ __($item['name']) }}</span>
                                <div class="transition-transform duration-200" :class="subOpen ? 'rotate-180' : ''">
                                    <i data-lucide="chevron-down"
                                        class="w-4 h-4 stroke-gray-500 dark:stroke-gray-400"></i>
                                </div>
                                @if (
                                    (function () use ($item, $page_slug) {
                                        foreach ($item['subitems'] as $subitem) {
                                            if (isset($subitem['active']) && $page_slug == $subitem['active']) {
                                                return true;
                                            }
                                        }
                                        return false;
                                    })())
                                    <div
                                        class="w-2 h-2 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse {{ $direction === 'ltr' ? 'ml-2' : 'mr-2' }}">
                                    </div>
                                @endif
                            </button>

                            <div x-show="subOpen" x-transition:enter="transition-all duration-200 ease-out"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition-all duration-150 ease-in"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="{{ $direction === 'ltr' ? 'ml-4' : 'mr-4' }} mt-1 space-y-1 {{ $direction === 'ltr' ? 'border-l-2' : 'border-r-2' }} border-gray-200 dark:border-gray-600 {{ $direction === 'ltr' ? 'pl-4' : 'pr-4' }}">

                                @foreach ($item['subitems'] as $subitem)
                                    @php
                                        $multiSubitemIcon = $subitem['icon'] ?? $defaultMultiSubitemIcon;
                                        $multiSubitemBoxicon = $subitem['boxicon'] ?? false;
                                    @endphp
                                    <a href="{{ $subitem['route'] }}"
                                        class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all duration-200 group {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800' : '' }}">
                                        <div
                                            class="w-6 h-6 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center group-hover:scale-105 transition-transform">
                                            @if ($multiSubitemBoxicon)
                                                <i
                                                    class="{{ $multiSubitemIcon }} text-xs {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-gray-500 dark:text-gray-500' }}"></i>
                                            @else
                                                <i data-lucide="{{ $multiSubitemIcon }}"
                                                    class="w-3 h-3 {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'stroke-violet-600 dark:stroke-violet-400' : 'stroke-gray-500 dark:stroke-gray-500' }}"></i>
                                            @endif
                                        </div>
                                        <span
                                            class="font-medium text-xs {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'text-violet-700 dark:text-violet-300' : 'text-gray-600 dark:text-gray-400' }} flex-1">{{ __($subitem['name']) }}</span>
                                        @if (isset($subitem['active']) && $page_slug == $subitem['active'])
                                            <div
                                                class="w-1.5 h-1.5 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse">
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item['route'] }}"
                            class="flex items-center gap-3 p-3 {{ $direction === 'ltr' ? 'mx-2' : 'mx-2' }} rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all duration-200 group {{ isset($item['active']) && $page_slug == $item['active'] ? 'bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800' : '' }}">
                            <div
                                class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform">
                                @if ($subitemBoxicon)
                                    <i
                                        class="{{ $subitemIcon }} text-sm {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-gray-600 dark:text-gray-400' }}"></i>
                                @else
                                    <i data-lucide="{{ $subitemIcon }}"
                                        class="w-4 h-4 {{ isset($item['active']) && $page_slug == $item['active'] ? 'stroke-violet-600 dark:stroke-violet-400' : 'stroke-gray-600 dark:stroke-gray-400' }}"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <span
                                    class="font-medium text-sm {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300' }}">{{ __($item['name']) }}</span>
                            </div>
                            @if (isset($item['active']) && $page_slug == $item['active'])
                                <div class="w-2 h-2 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse"></div>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Expanded Dropdown (for full sidebar) --}}
    @if ($type === 'dropdown' && count($items) > 0)
        <div x-show="open && ((desktop && sidebar_expanded) || (!desktop && mobile_menu_open))"
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-95 max-h-0"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100 max-h-[500px]"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100 max-h-[500px]"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95 max-h-0"
            class="{{ $direction === 'ltr' ? 'ml-4' : 'mr-4' }} mt-2 space-y-1 {{ $direction === 'ltr' ? 'border-l-2' : 'border-r-2' }} border-bg-black/10 dark:border-bg-white/10 {{ $direction === 'ltr' ? 'pl-4' : 'pr-4' }} hidden overflow-hidden"
            :class="(open && ((desktop && sidebar_expanded) || (!desktop && mobile_menu_open)) ? '!block' : '!hidden')">

            @foreach ($items as $item)
                @php
                    $subitemIcon = $item['icon'] ?? $defaultSubitemIcon;
                    $subitemBoxicon = $item['boxicon'] ?? false;
                @endphp

                @if (isset($item['type']) && $item['type'] === 'single')
                    <a href="{{ $item['route'] }}"
                        class="sidebar-item flex items-center gap-4 p-2 rounded-lg hover:bg-bg-black/5 dark:hover:bg-bg-white/5 text-text-white transition-all duration-200 group {{ isset($item['active']) && $page_slug == $item['active'] ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                        <div
                            class="w-6 h-6 bg-bg-black/5 dark:bg-bg-white/5 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform">
                            @if ($subitemBoxicon)
                                <i class="{{ $subitemIcon }} text-xs"></i>
                            @else
                                <i data-lucide="{{ $subitemIcon }}" class="w-3 h-3 stroke-current"></i>
                            @endif
                        </div>
                        <span
                            class="font-medium text-sm {{ $direction === 'ltr' ? 'text-left' : 'text-right' }} {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-text-light-secondary dark:text-text-dark-primary' }}">{{ __($item['name']) }}</span>
                    </a>
                @elseif (isset($item['subitems']) && count($item['subitems']) > 0)
                    <div x-data="{
                        subOpen: {{ (function () use ($item, $page_slug) {
                            foreach ($item['subitems'] as $subitem) {
                                if (isset($subitem['active']) && $page_slug == $subitem['active']) {
                                    return 'true';
                                }
                            }
                            return 'false';
                        })() }}
                    }">
                        <button @click="subOpen = !subOpen"
                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-bg-black/5 dark:hover:bg-bg-white/5 text-text-light-secondary dark:text-text-dark-primary transition-all duration-200 w-full group">
                            <div
                                class="w-6 h-6 bg-bg-black/5 dark:bg-bg-white/5 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform">
                                @if ($subitemBoxicon)
                                    <i class="{{ $subitemIcon }} text-xs"></i>
                                @else
                                    <i data-lucide="{{ $subitemIcon }}" class="w-3 h-3 stroke-current"></i>
                                @endif
                            </div>
                            <span
                                class="font-medium text-sm flex-1 {{ $direction === 'ltr' ? 'text-left' : 'text-right' }}">{{ __($item['name']) }}</span>
                            <div class="transition-transform duration-200" :class="subOpen ? 'rotate-180' : ''">
                                <i data-lucide="chevron-down" class="w-3 h-3 stroke-current"></i>
                            </div>
                        </button>

                        <div x-show="subOpen" x-transition:enter="transition-all duration-200 ease-out"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition-all duration-150 ease-in"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="{{ $direction === 'ltr' ? 'ml-6' : 'mr-6' }} mt-1 space-y-1 {{ $direction === 'ltr' ? 'border-l' : 'border-r' }} border-bg-black/5 dark:border-bg-white/5 {{ $direction === 'ltr' ? 'pl-3' : 'pr-3' }}">

                            @foreach ($item['subitems'] as $subitem)
                                @php
                                    $multiSubitemIcon = $subitem['icon'] ?? $defaultMultiSubitemIcon;
                                    $multiSubitemBoxicon = $subitem['boxicon'] ?? false;
                                @endphp
                                <a href="{{ $subitem['route'] }}"
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-bg-black/5 dark:hover:bg-bg-white/5 transition-all duration-200 group {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                                    <div
                                        class="w-5 h-5 bg-bg-black/5 dark:bg-bg-white/5 rounded flex items-center justify-center group-hover:scale-110 transition-transform">
                                        @if ($multiSubitemBoxicon)
                                            <i class="{{ $multiSubitemIcon }} text-xs"></i>
                                        @else
                                            <i data-lucide="{{ $multiSubitemIcon }}"
                                                class="w-2.5 h-2.5 stroke-current"></i>
                                        @endif
                                    </div>
                                    <span
                                        class="font-medium text-xs {{ isset($subitem['active']) && $page_slug == $subitem['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-text-light-secondary dark:text-text-dark-primary' }}">{{ __($subitem['name']) }}</span>
                                    @if (isset($subitem['active']) && $page_slug == $subitem['active'])
                                        <div class="{{ $direction === 'ltr' ? 'ml-auto' : 'mr-auto' }}">
                                            <div
                                                class="w-1.5 h-1.5 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse">
                                            </div>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['route'] }}"
                        class="flex items-center gap-3 p-2 rounded-lg hover:bg-bg-black/5 dark:hover:bg-bg-white/5 transition-all duration-200 group {{ isset($item['active']) && $page_slug == $item['active'] ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                        <div
                            class="w-6 h-6 bg-bg-black/5 dark:bg-bg-white/5 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform">
                            @if ($subitemBoxicon)
                                <i class="{{ $subitemIcon }} text-xs"></i>
                            @else
                                <i data-lucide="{{ $subitemIcon }}" class="w-3 h-3 stroke-current"></i>
                            @endif
                        </div>
                        <span
                            class="font-medium text-sm {{ isset($item['active']) && $page_slug == $item['active'] ? 'text-violet-600 dark:text-violet-400' : 'text-text-light-secondary dark:text-text-dark-primary' }}">{{ __($item['name']) }}</span>
                        @if (isset($item['active']) && $page_slug == $item['active'])
                            <div class="{{ $direction === 'ltr' ? 'ml-auto' : 'mr-auto' }}">
                                <div class="w-1.5 h-1.5 bg-violet-400 dark:bg-violet-300 rounded-full animate-pulse">
                                </div>
                            </div>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
