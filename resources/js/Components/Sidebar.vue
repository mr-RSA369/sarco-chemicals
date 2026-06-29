<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    HomeIcon,
    PlusIcon,
    BeakerIcon,
    CubeIcon,
    ArchiveBoxIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    ChartBarIcon,
    DocumentTextIcon,
    XMarkIcon,
    Bars3Icon
} from '@heroicons/vue/24/outline'

const page = usePage()

const user = computed(() => page.props.auth.user)




// State
const tooltipKey = ref(0) // Used to force tooltip re-render for position updates
const isExpanded = ref(
    typeof window !== 'undefined'
        ? window.innerWidth >= 768
        : true
)



const isMobile = ref(
    typeof window !== 'undefined'
        ? window.innerWidth < 768
        : false
)

const isSidebarVisible = isMobile.value===false ? ref(true) : ref(false)

const expandedMenus = ref(new Set())
const hoveredMenu = ref(null)
const tooltipPosition = ref({x: 0, y: 0})
const updateTooltipPosition = (event) => {
    tooltipPosition.value = {
        x: event.clientX + 18,
        y: event.clientY
    }
}
const showTooltip = ref(false)
const tooltipText = ref('')
const tooltipSubItem = ref(null)
const tooltipTimer = ref(null)
const isTransitioning = ref(false)

// Check mobile
const checkMobile = () => {

    const mobile =
        window.innerWidth < 768

    // Desktop -> Mobile
    if (
        mobile &&
        !isMobile.value
    ) {
        isExpanded.value = false
    }

    // Mobile -> Desktop
    if (
        !mobile &&
        isMobile.value
    ) {
        isExpanded.value = true
    }

    isMobile.value = mobile
}

// Initialize
if (typeof window !== 'undefined') {
    checkMobile()
    window.addEventListener('resize', checkMobile)
}

// Toggle sidebar
const toggleSidebar = () => {
    isExpanded.value = !isExpanded.value
    if (!isExpanded.value) {
        expandedMenus.value.clear()
        hoveredMenu.value = null
        showTooltip.value = false
        isTransitioning.value = false
        if (tooltipTimer.value) {
            clearTimeout(tooltipTimer.value)
            tooltipTimer.value = null
        }
    }
}

// Handle click on main option
const handleMainClick = (menuId, event) => {
    if (event) event.preventDefault()

    if (expandedMenus.value.has(menuId)) {
        expandedMenus.value.delete(menuId)
    } else {
        expandedMenus.value.add(menuId)
    }
}

// Handle hover for closed mode tooltips with position tracking
const handleHoverWithPosition = (item, event) => {

    if (isExpanded.value) return

    if (tooltipTimer.value) {
        clearTimeout(tooltipTimer.value)
        tooltipTimer.value = null
    }

    updateTooltipPosition(event)

    hoveredMenu.value = item.id
    tooltipSubItem.value = null
    tooltipText.value = item.name

    showTooltip.value = false

    requestAnimationFrame(() => {
        showTooltip.value = true
    })
}

// Handle hover for suboptions with position tracking
const handleSubHoverWithPosition = (
    subItem,
    event
) => {

    if (isExpanded.value) return

    if (tooltipTimer.value) {
        clearTimeout(tooltipTimer.value)
        tooltipTimer.value = null
    }

    updateTooltipPosition(event)

    tooltipSubItem.value = subItem.name
    tooltipText.value = subItem.name

    showTooltip.value = false

    requestAnimationFrame(() => {

        showTooltip.value = true
    })
}

const handleLeaveWithPosition = () => {

    if (isExpanded.value) return

    if (tooltipTimer.value) {
        clearTimeout(tooltipTimer.value)
    }

    tooltipTimer.value = setTimeout(() => {

        showTooltip.value = false
        hoveredMenu.value = null
        tooltipSubItem.value = null

    }, 100)
}

// Close sidebar on mobile
const closeMobile = () => {
    if (isMobile.value) {
        isExpanded.value = false
        expandedMenus.value.clear()
        showTooltip.value = false
        isTransitioning.value = false
        if (tooltipTimer.value) {
            clearTimeout(tooltipTimer.value)
            tooltipTimer.value = null
        }
    }
}

// Navigation items
const navItems = [
    {
        id: 'dashboard',
        name: 'Dashboard',
        icon: HomeIcon,
        href: '/dashboard',
        hasDropdown: false
    },
    {
        id: 'bottles',
        name: 'Bottles',
        icon: CubeIcon,
        href: '/bottles',
        hasDropdown: true,
        dropdownItems: [
            { name: 'Add Bottles', href: '/bottles/create', icon: PlusIcon, role:user.value.role },
            { name: 'View Items', href: '/bottles', icon: DocumentTextIcon, role:'allowed' },
            { name: 'Transactions', href: '/bottles/transactions', icon: ChartBarIcon, role:user.value.role }
        ]
    },
    {
        id: 'packing',
        name: 'Packing Materials',
        icon: ArchiveBoxIcon,
        href: '/packing-materials',
        hasDropdown: true,
        dropdownItems: [
            { name: 'Add Packing Materials', href: '/packing-materials/create', icon: PlusIcon, role:user.value.role },
            { name: 'View Items', href: '/packing-materials', icon: DocumentTextIcon, role:'allowed' },
            { name: 'Transactions', href: '/packing-materials/transactions', icon: ChartBarIcon, role:user.value.role }
        ]
    },
    {
        id: 'chemicals',
        name: 'Chemicals',
        icon: BeakerIcon,
        href: '/chemicals',
        hasDropdown: true,
        dropdownItems: [
            { name: 'Add Chemicals', href: '/chemicals/create', icon: PlusIcon, role:user.value.role },
            { name: 'View Items', href: '/chemicals', icon: DocumentTextIcon, role:'allowed' },
            { name: 'Transactions', href: '/chemicals/transactions', icon: ChartBarIcon, role:user.value.role }
        ]
    }
]

// Check if menu is expanded
const isMenuExpanded = (menuId) => {
    return expandedMenus.value.has(menuId)
}

// Check if menu is active
const isMenuActive = (item) => {
    if (!item.hasDropdown) {
        return page.url === item.href
    }
    return page.url.startsWith(item.href) ||
           item.dropdownItems?.some(sub => page.url === sub.href)
}

// Check if sub item is active
const isSubItemActive = (subItem) => {
    return page.url === subItem.href
}

// Click outside handler
const handleClickOutside = (event) => {
    if (expandedMenus.value.size > 0) {
        const sidebar = document.querySelector('.sidebar-container')
        if (sidebar && !sidebar.contains(event.target)) {
            expandedMenus.value.clear()
        }
    }
}

// Lifecycle
onMounted(() => {
    checkMobile()
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
    document.removeEventListener('click', handleClickOutside)
    if (tooltipTimer.value) {
        clearTimeout(tooltipTimer.value)
        tooltipTimer.value = null
    }
})
</script>

<template>
    <!-- Mobile Overlay -->
    <transition name="fade">
        <div
            v-if="isMobile && isExpanded"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
            @click="toggleSidebar"
        />
    </transition>

    <!-- Sidebar -->
    <aside @mousemove="updateTooltipPosition"
        class="sidebar-container fixed md:relative sm:relative z-50 h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-2xl transition-all duration-300 overflow-visible"
        :class="[
            isSidebarVisible
                ? (isExpanded ? 'w-72' : 'w-20')
                : 'w-0'
        ]"
    >
        <!-- Gradient Border -->
        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 animate-gradient-x z-10" />

        <!-- Header - Fixed at top -->
        <div class="relative p-4 border-b border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex-shrink-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <!-- Logo -->
                    <div class="relative flex-shrink-0">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-xl blur-lg opacity-50" />
                        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-600 flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">S</span>
                        </div>
                    </div>

                    <!-- Brand Name -->
                    <transition name="slide">
                        <span
                            v-if="isExpanded"
                            class="text-xl font-bold bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 bg-clip-text text-transparent whitespace-nowrap"
                        >
                            SARCO Chemicals
                        </span>
                    </transition>
                </div>

                <!-- Toggle Button -->
                <button
                    @click="toggleSidebar"
                    class="absolute -right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white shadow-lg transition-all duration-200 md:flex hidden border border-white/10"
                >
                    <ChevronLeftIcon v-if="isExpanded" class="w-4 h-4" />
                    <ChevronRightIcon v-else class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Scrollable Navigation Area -->
        <div
            class="flex-1 overflow-y-auto overflow-x-visible py-4 px-3 custom-scrollbar"
            :style="{
                maxHeight: 'calc(100vh - 140px)',
                height: 'auto'
            }"
        >
            <div class="space-y-2">
                <template v-for="item in navItems" :key="item.id">
                    <!-- Main Navigation Item -->
                    <div
                        class="relative"
                        @mouseenter="handleHoverWithPosition(item, $event)"
                        @mouseleave="handleLeaveWithPosition"
                    >
                        <!-- Item Container -->
                        <Link
                            v-if="!item.hasDropdown"
                            :href="item.href"
                            @click="closeMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative"
                            :class="[
                                isMenuActive(item)
                                    ? 'bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 text-white border border-emerald-500/30'
                                    : 'text-gray-400 hover:text-white hover:bg-white/5'
                            ]"
                        >
                            <!-- Active Indicator -->
                            <div
                                v-if="isMenuActive(item)"
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-gradient-to-b from-emerald-400 to-cyan-400 rounded-r"
                            />

                            <!-- Icon -->
                            <component
                                :is="item.icon"
                                class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-110"
                                :class="{ 'text-emerald-400': isMenuActive(item) }"
                            />

                            <!-- Label (only in expanded mode) -->
                            <transition name="slide">
                                <span
                                    v-if="isExpanded"
                                    class="flex-1 text-sm font-medium whitespace-nowrap"
                                >
                                    {{ item.name }}
                                </span>
                            </transition>
                        </Link>

                        <!-- Item Container with Dropdown -->
                        <div
                            v-else
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative cursor-pointer"
                            :class="[
                                isMenuExpanded(item.id) || isMenuActive(item)
                                    ? 'bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 text-white border border-emerald-500/30'
                                    : 'text-gray-400 hover:text-white hover:bg-white/5'
                            ]"
                            @click="handleMainClick(item.id, $event)"
                        >
                            <!-- Active Indicator -->
                            <div
                                v-if="isMenuActive(item)"
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-gradient-to-b from-emerald-400 to-cyan-400 rounded-r"
                            />

                            <!-- Icon -->
                            <component
                                :is="item.icon"
                                class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-110"
                                :class="{ 'text-emerald-400': isMenuActive(item) }"
                            />

                            <!-- Label (only in expanded mode) -->
                            <transition name="slide">
                                <span
                                    v-if="isExpanded"
                                    class="flex-1 text-sm font-medium whitespace-nowrap"
                                >
                                    {{ item.name }}
                                </span>
                            </transition>

                            <!-- Dropdown Arrow (only in expanded mode) -->
                            <ChevronDownIcon
                                v-if="item.hasDropdown && isExpanded"
                                class="w-4 h-4 transition-transform duration-200 flex-shrink-0"
                                :class="{ 'rotate-180': isMenuExpanded(item.id) }"
                            />
                        </div>

                        <!-- ============================================ -->
                        <!-- EXPANDED MODE: Suboptions (inline dropdown) -->
                        <!-- ============================================ -->
                        <transition name="expand-dropdown">
                            <div
                                v-if="isExpanded && item.hasDropdown && isMenuExpanded(item.id)"
                                class="mt-1 ml-9 space-y-1"
                            >
                                <div class="relative pl-4">
                                    <div class="absolute left-0 top-0 bottom-0 w-px bg-gradient-to-b from-emerald-500/30 to-cyan-500/30" />

                                    <div class="space-y-1">
                                        <Link
                                            v-for="subItem in item.dropdownItems"
                                            :key="subItem.name"
                                            :href="subItem.href"
                                            @click="closeMobile()"
                                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200"
                                            :class="[
                                                isSubItemActive(subItem)
                                                    ? 'text-emerald-400 bg-emerald-500/10'
                                                    : 'text-gray-500 hover:text-gray-300 hover:bg-white/5'
                                            ]"
                                        >
                                            <component v-if="subItem.role!=='user'" :is="subItem.icon" class="w-4 h-4 flex-shrink-0" />
                                            <span v-if="subItem.role!=='user'" class="whitespace-nowrap">{{ subItem.name }}</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <!-- ============================================ -->
                        <!-- CLOSED MODE: Suboptions (icons below)       -->
                        <!-- ============================================ -->
                        <div
                            v-if="!isExpanded && item.hasDropdown && isMenuExpanded(item.id)"
                            class="relative right-1 mt-1 space-y-1 pl-2"
                        >
                            <div class="space-y-1">
                                <Link
                                    v-for="subItem in item.dropdownItems"
                                    :key="subItem.name"
                                    :href="subItem.href"
                                    @click="closeMobile()"
                                    @mouseenter="handleSubHoverWithPosition(subItem, $event)"
                                    @mouseleave="handleLeaveWithPosition"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-200 group/sub"
                                    :class="[
                                        isSubItemActive(subItem)
                                            ? 'text-emerald-400 bg-emerald-500/10'
                                            : 'text-gray-400 hover:text-white '
                                    ]"
                                >
                                    <div class="w-4 h-4 rounded-lg from-slate-700 to-slate-600 flex items-center justify-center flex-shrink-0 transition-all duration-200 group-hover/sub:scale-110 group-hover/sub:shadow-lg">
                                        <component v-if="subItem.role!=='user'" :is="subItem.icon" class="w-4 h-4" />
                                    </div>
                                    <!-- Suboption name (hidden, just for structure) -->
                                    <span class="text-xs font-medium whitespace-nowrap opacity-0">
                                        {{ subItem.name }}
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer - Fixed at bottom -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 bg-gradient-to-t from-slate-900/90 to-transparent backdrop-blur-sm flex-shrink-0 z-10">
            <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-full blur-md opacity-50" />
                    <div class="relative w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-600 flex items-center justify-center shadow-lg">
                        <span class="text-white text-xs font-bold">JD</span>
                    </div>
                </div>

                <transition name="slide">
                    <div v-if="isExpanded" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">John Doe</p>
                        <p class="text-xs text-emerald-400 truncate">Administrator</p>
                    </div>
                </transition>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Toggle -->
<button
    v-if="isMobile"
    @click="isSidebarVisible = !isSidebarVisible"
    :class="[
        isSidebarVisible
            ? 'left-[56px] top-12 scale-100 rotate-0'
            : 'left-3 top-12 scale-95 -rotate-180'
    ]"
    class="
        fixed
        z-[9999]

        w-9
        h-9

        rounded-full

        bg-gradient-to-br
        from-slate-800
        to-slate-900

        hover:from-slate-700
        hover:to-slate-800

        text-white

        flex
        items-center
        justify-center

        border
        border-white/10

        transition-all
        duration-500
        ease-[cubic-bezier(.34,1.56,.64,1)]

        hover:scale-110
        active:scale-95
    "
>
    <Transition
        mode="out-in"
        enter-active-class="transition-all duration-300"
        leave-active-class="transition-all duration-200 absolute"
        enter-from-class="opacity-0 scale-50 rotate-90"
        enter-to-class="opacity-100 scale-100 rotate-0"
        leave-from-class="opacity-100 scale-100 rotate-0"
        leave-to-class="opacity-0 scale-50 -rotate-90"
    >
        <Bars3Icon
            v-if="!isSidebarVisible"
            key="menu"
            class="w-4 h-4"
        />

        <XMarkIcon
            v-else
            key="close"
            class="w-4 h-4"
        />
    </Transition>
</button>

    <!-- ============================================ -->
    <!-- TELEPORTED TOOLTIP - Shows outside sidebar   -->
    <!-- ============================================ -->
    <Teleport to="body">
        <transition name="tooltip-grow">
            <div
                v-if="showTooltip && !isExpanded && (hoveredMenu || tooltipSubItem)"
                class="fixed z-[9999] pointer-events-none"
                :style="{
                    left: tooltipPosition.x + 'px',
                    top: tooltipPosition.y + 'px'
                }"
            >
                <div class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-slate-900/95 to-slate-800/95 backdrop-blur-md border border-emerald-500/30 shadow-2xl shadow-emerald-500/10 overflow-hidden">
                    <div class="flex items-center gap-2.5">
                        <!-- Small indicator dot -->
                        <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 animate-pulse flex-shrink-0"></div>
                        <span class="text-sm font-medium text-white whitespace-nowrap tracking-wide">
                            {{ tooltipText }}
                        </span>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
/* Custom Scrollbar - Enhanced */
.custom-scrollbar {
    scroll-behavior: smooth;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    transition: background 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Firefox scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.2) rgba(255, 255, 255, 0.05);
}

/* Animations */
@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-x 3s ease infinite;
}

/* Tooltip Grow Animation - From 0 to full width */
@keyframes tooltip-grow-in {
    0% {
        opacity: 0;
        transform: scaleX(0);
        transform-origin: left center;
        max-width: 0;
    }
    40% {
        opacity: 1;
        transform: scaleX(1.02);
        transform-origin: left center;
        max-width: 200px;
    }
    70% {
        transform: scaleX(0.98);
        transform-origin: left center;
        max-width: 200px;
    }
    100% {
        opacity: 1;
        transform: scaleX(1);
        transform-origin: left center;
        max-width: 200px;
    }
}

@keyframes tooltip-grow-out {
    0% {
        opacity: 1;
        transform: scaleX(1);
        transform-origin: left center;
        max-width: 200px;
    }
    100% {
        opacity: 0;
        transform: scaleX(0);
        transform-origin: left center;
        max-width: 0;
    }
}

/* Tooltip Grow Transition */
.tooltip-grow-enter-active {
    animation: tooltip-grow-in 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.tooltip-grow-leave-active {
    animation: tooltip-grow-out 0.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}

.expand-dropdown-enter-active,
.expand-dropdown-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.expand-dropdown-enter-from,
.expand-dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.hover-popup-enter-active,
.hover-popup-leave-active {
    transition: all 0.2s ease;
}
.hover-popup-enter-from,
.hover-popup-leave-to {
    opacity: 0;
    transform: scale(0.95) translateX(-5px);
}

/* Utility */
* {
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

/* Hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Ensure sidebar doesn't overflow */
.sidebar-container {
    display: flex;
    flex-direction: column;
    overflow: visible;
}

/* Adjust for mobile */
@media (max-width: 768px) {
    .sidebar-container {
        height: 100vh;
        max-height: 100vh;
        overflow: hidden;
    }
}
</style>
