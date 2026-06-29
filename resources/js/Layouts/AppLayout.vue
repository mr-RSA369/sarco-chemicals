<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Topbar from '@/Components/Topbar.vue'
import { ref, onMounted, onUnmounted } from 'vue'

const isSidebarOpen = ref(false)

// Listen for sidebar toggle events
const handleSidebarToggle = (event) => {
    isSidebarOpen.value = event.detail?.isOpen ?? !isSidebarOpen.value
}

onMounted(() => {
    window.addEventListener('sidebar-toggle', handleSidebarToggle)
})

onUnmounted(() => {
    window.removeEventListener('sidebar-toggle', handleSidebarToggle)
})
</script>

<template>
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <Sidebar @toggle="isSidebarOpen = !isSidebarOpen" />

        <div
            class="flex-1 flex flex-col min-w-0 transition-all duration-300"
            :class="{
                'md:ml-0': true,
                'ml-72': isSidebarOpen,
                'ml-0': !isSidebarOpen
            }"
        >
            <Topbar />
            <main class="flex-1 overflow-y-auto bg-gray-50/50">
                <div class="max-w-7xl max-h-[80vh] mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
