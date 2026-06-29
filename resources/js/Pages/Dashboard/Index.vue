<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import {
  CubeIcon,
  ClipboardDocumentListIcon,
  BeakerIcon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  CalendarDaysIcon,
  TagIcon,
  BuildingStorefrontIcon,
  UserIcon
} from '@heroicons/vue/24/outline'

import AppLayout from '@/Layouts/AppLayout.vue'

const successMessage = ref('')

const transactions = ref([])

const page = ref(1)

const hasMore = ref(true)

const loading = ref(false)

const transactionContainer = ref(null)

const formatDate = (date) => {

    if (!date) return ''

    return new Date(date)
        .toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
}

const loadTransactions = async () => {

    if (
        loading.value ||
        !hasMore.value
    ) return

    try {

        loading.value = true

        const { data } = await axios.get(
            '/api/dashboard/latest-transactions',
            {
                params: {
                    page: page.value,
                    per_page: 10
                }
            }
        )

        transactions.value.push(
            ...data.data
        )

        page.value++

        hasMore.value =
            data.current_page <
            data.last_page

    } finally {

        loading.value = false
    }
}

const handleScroll = () => {

    const el =
        transactionContainer.value

    if (!el) return

    if (
        el.scrollTop +
        el.clientHeight >=
        el.scrollHeight - 80
    ) {
        loadTransactions()
    }
}

const badgeClass = (type) => {

    return type === 'receipt'
        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
        : 'bg-rose-50 text-rose-700 border-rose-200'
}

const getStockStatusColor = (stock) => {
    if (stock <= 5) return 'text-rose-600'
    if (stock <= 15) return 'text-amber-600'
    return 'text-emerald-600'
}

onMounted(() => {

    loadTransactions()

})
</script>

<template>

<AppLayout>

    <!-- Success Message -->
    <div
        v-if="successMessage"
        class="mb-6 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm"
    >
        <CheckCircleIcon class="w-5 h-5 text-emerald-600 flex-shrink-0" />
        <span class="font-medium">{{ successMessage }}</span>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 m-4">

        <div class="group relative bg-white rounded-2xl shadow-sm border border-cyan-600/60 p-6 transition-all duration-300 hover:shadow-xl hover:border-cyan-800/80 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-xl">
                        <CubeIcon class="w-5 h-5 text-cyan-700" />
                    </div>
                    <span class="text-xs font-medium text-cyan-600 bg-cyan-50 px-2.5 py-1 rounded-full border border-cyan-200">+12%</span>
                </div>
                <h3 class="text-sm font-medium text-slate-600">Total Bottles</h3>
                <p class="text-3xl font-bold text-slate-800 mt-1.5 tracking-tight">1,284</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600">
                    <ArrowPathIcon class="w-3 h-3" />
                    <span>12 new this week</span>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-violet-600/60 p-6 transition-all duration-300 hover:shadow-xl hover:border-violet-800/80 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-gradient-to-br from-violet-100 to-violet-200 rounded-xl">
                        <ClipboardDocumentListIcon class="w-5 h-5 text-violet-700" />
                    </div>
                    <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full border border-violet-200">+8%</span>
                </div>
                <h3 class="text-sm font-medium text-slate-600">Packing Materials</h3>
                <p class="text-3xl font-bold text-slate-800 mt-1.5 tracking-tight">847</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                    <BuildingStorefrontIcon class="w-3 h-3" />
                    <span>3 suppliers active</span>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-amber-600/60 p-6 transition-all duration-300 hover:shadow-xl hover:border-amber-800/80 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl">
                        <BeakerIcon class="w-5 h-5 text-amber-700" />
                    </div>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">-2%</span>
                </div>
                <h3 class="text-sm font-medium text-slate-600">Chemicals</h3>
                <p class="text-3xl font-bold text-slate-800 mt-1.5 tracking-tight">392</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-600">
                    <UserIcon class="w-3 h-3" />
                    <span>5 items expiring soon</span>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-rose-600/60 p-6 transition-all duration-300 hover:shadow-xl hover:border-rose-800/80 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-gradient-to-br from-rose-100 to-rose-200 rounded-xl">
                        <ExclamationTriangleIcon class="w-5 h-5 text-rose-700" />
                    </div>
                    <span class="text-xs font-medium text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">Critical</span>
                </div>
                <h3 class="text-sm font-medium text-slate-600">Low Stock</h3>
                <p class="text-3xl font-bold text-rose-600 mt-1.5 tracking-tight">23</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-rose-600">
                    <ArrowPathIcon class="w-3 h-3" />
                    <span>Order restock</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Transactions -->
    <div class="bg-emerald-100 mx-4 mb-8 rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden transition-all duration-300 hover:shadow-lg">

        <!-- Header -->
        <div class="px-6 py-2 border-b border-slate-400/60 flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-200">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl">
                    <ClipboardDocumentListIcon class="w-5 h-5 text-indigo-700" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Recent Transactions</h2>
                    <p class="text-xs text-slate-600">Latest inventory movements</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3.5 py-1.5 rounded-full border border-indigo-200 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse"></span>
                    {{ transactions.length }} loaded
                </span>
            </div>
        </div>

        <!-- Scrollable Table -->
        <div
            ref="transactionContainer"
            @scroll="handleScroll"
            class="max-h-[40vh] overflow-auto custom-scrollbar"
        >

            <table class="min-w-[340px] w-full table-auto text-sm">

                <thead class="sticky top-0 z-10 bg-emerald-300/70 backdrop-blur-sm border-b border-slate-200 rounded-lg">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <CalendarDaysIcon class="w-3.5 h-3.5" />
                                Date
                            </div>
                        </th>
                        <th class="px-5 py-1 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <TagIcon class="w-3.5 h-3.5" />
                                Item
                            </div>
                        </th>
                        <th class="px-5 py-1 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-5 py-1 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th class="px-5 py-1 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            Remarks
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="(transaction, index) in transactions"
                        :key="transaction.id"
                        class="group bg-emerald-100 hover:bg-emerald-200/50 transition-all duration-200"
                    >

                        <!-- Date -->
                        <td class="px-5 py-1 whitespace-nowrap text-slate-600 font-medium">
                            {{ formatDate(transaction.transaction_date) }}
                        </td>

                        <!-- Item -->
                        <td class="px-5 py-1">
                            <div class="font-semibold text-slate-800">
                                {{ transaction.item?.item_name || 'Unknown Item' }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ transaction.item?.size || 'N/A' }}
                                {{ transaction.item?.size_unit || '' }}
                            </div>
                        </td>

                        <!-- Type -->
                        <td class="px-5 py-1 text-center whitespace-nowrap">
                            <span
                                class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium border transition-all duration-200 group-hover:scale-105"
                                :class="badgeClass(transaction.transaction_type)"
                            >
                                {{ transaction.transaction_type }}
                            </span>
                        </td>

                        <!-- Quantity -->
                        <td class="px-5 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center font-bold text-slate-700 min-w-[40px]">
                                {{ transaction.quantity }}
                            </span>
                        </td>

                        <!-- Remarks -->
                        <td class="px-5 py-1 text-slate-600 max-w-[200px] truncate">
                            {{ transaction.remarks || '—' }}
                        </td>

                    </tr>
                </tbody>

            </table>

            <!-- Loading State -->
            <div v-if="loading" class="py-4 text-center">
                <div class="inline-flex items-center gap-3 text-slate-500">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-medium">Loading transactions...</span>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && transactions.length === 0" class="py-12 text-center">
                <div class="inline-flex flex-col items-center gap-3">
                    <div class="p-4 bg-slate-100 rounded-full">
                        <ClipboardDocumentListIcon class="w-8 h-8 text-slate-400" />
                    </div>
                    <p class="text-slate-500 font-medium">No transactions found</p>
                    <p class="text-sm text-slate-400">Transactions will appear here once available</p>
                </div>
            </div>

            <!-- Finished -->
            <div
                v-if="!hasMore && transactions.length > 0"
                class="py-2 text-center border-t border-slate-100"
            >
                <p class="text-sm text-slate-600 font-medium">All transactions loaded</p>
            </div>

        </div>

    </div>

</AppLayout>

</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(241, 245, 249, 0.8);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #818CF8, #6366F1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #6366F1, #4F46E5);
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #818CF8 rgba(241, 245, 249, 0.8);
}

/* Smooth hover transitions */
.group-hover\:scale-105 {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Card hover animations */
.rounded-2xl {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Table row striping improvement */
tbody tr {
    transition: background-color 0.2s ease;
}

/* Better focus states */
*:focus-visible {
    outline: 2px solid #818CF8;
    outline-offset: 2px;
}

/* Loading spinner animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 0.8s linear infinite;
}

/* Pulse animation for live indicator */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
