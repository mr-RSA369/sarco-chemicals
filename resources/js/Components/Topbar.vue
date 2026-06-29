<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const page = usePage()

const user = computed(() => page.props.auth.user)

const open = ref(false)

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">

        <h1 class="text-xl font-semibold">
            Dashboard
        </h1>

        <div class="relative">
            <button
                @click="open = !open"
                class="flex items-center gap-2"
            >
                <div
                    class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center"
                >
                    {{ user?.name?.charAt(0).toUpperCase() }}
                </div>

                <span>{{ user?.name }}</span>
            </button>

            <div
    v-if="open"
    class="
        absolute
        right-0
        mt-3
        w-52
        overflow-hidden
        rounded-xl
        border
        border-gray-200
        bg-white
        shadow-xl
        ring-1
        ring-black/5
        z-50
    "
>
    <!-- Header -->
    <div class="px-4 py-3 border-b bg-gray-50">
        <p class="text-sm font-semibold text-gray-800">
            {{ user?.name }}
        </p>

        <p class="text-xs text-gray-500 truncate">
            {{ user?.email }}
        </p>
    </div>

    <!-- Menu -->
    <a
        href="/profile"
        class="
            flex
            items-center
            px-4
            py-3
            text-sm
            text-gray-700
            transition-colors
            duration-200
            hover:bg-emerald-50
            hover:text-emerald-600
        "
    >

        <span class="ml-3">
            Profile
        </span>
    </a>

    <button
        @click="logout"
        class="
            w-full
            flex
            items-center
            px-4
            py-3
            text-sm
            text-red-600
            transition-colors
            duration-200
            hover:bg-red-50
            hover:text-red-700
        "
    >

        <span class="ml-3">
            Logout
        </span>
    </button>
</div>
        </div>

    </header>
</template>
