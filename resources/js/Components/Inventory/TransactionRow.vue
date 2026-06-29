<script setup>

import { ref } from 'vue'

const tooltipVisible = ref(false)

const tooltipPosition = ref({
    x: 0,
    y: 0
})

const hoveredTransaction = ref(null)

const showTooltip = (
    transaction,
    event
) => {

    if (
        !transaction.item?.bundle_sizes?.length
    ) {
        return
    }

    hoveredTransaction.value = transaction

    tooltipPosition.value = {
        x: event.clientX - 48,
        y: event.clientY + 15
    }

    tooltipVisible.value = true
}

const hideTooltip = () => {

    tooltipVisible.value = false

    hoveredTransaction.value = null
}

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    },

    categoryId: {
        type: Number,
        required: true
    }
})

defineEmits([
    'delete'
])

const formatDate = (date) => {

    if (!date) return ''

    return new Date(date)
        .toLocaleDateString()
}


</script>

<template>

<tr
    class="border-b hover:bg-gray-50"
>

    <!-- Date -->
    <td class="p-2 whitespace-nowrap">
        {{ formatDate(transaction.transaction_date) }}
    </td>

    <!-- Product -->
    <td class="p-2">
        {{ transaction.item?.item_name }}
        {{ transaction.item?.size }}
        {{ transaction.item?.size_unit }}
    </td>

    <!-- PM Only -->
    <template
        v-if="categoryId === 2"
    >

        <td class="p-2">
            {{ transaction.batch?.batch_no }}
        </td>

        <td class="p-2 whitespace-nowrap">
            {{ transaction.batch?.mfg_date }}
        </td>

        <td class="p-2 whitespace-nowrap">
            {{ transaction.batch?.exp_date }}
        </td>

        <td class="p-2">
            {{ transaction.batch?.rack_no }}
        </td>

    </template>

    <!-- Opening Balance -->
    <td class="p-2 text-center">
        {{ transaction.opening_balance }}
    </td>

    <!-- Transaction Type -->
    <td class="p-2 text-center">

        <span
            v-if="transaction.transaction_type === 'receipt'"
            class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-medium"
        >
            Receipt
        </span>

        <span
            v-else
            class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-medium"
        >
            Issue
        </span>

    </td>

    <!-- Bundle Size -->
    <td class="p-2 text-center">

        {{ transaction.bundle_size?.bundle_size }}

    </td>

    <!-- Bundle Count -->
    <td class="p-2 text-center">

        {{ transaction.bundle_count }}

    </td>

    <!-- Quantity -->
    <td class="p-2 text-center font-medium">

        {{ transaction.quantity }}

    </td>

    <!-- Closing Balance -->
    <td class="p-2 text-center font-medium">
        <span
            @mouseenter="
                showTooltip(
                    transaction,
                    $event
                )
            "
            @mouseleave="hideTooltip"
            class="cursor-pointer text-blue-600"
        >
            {{ transaction.closing_balance }}
        </span>

    </td>

    <!-- Remarks -->
    <td class="p-2">

        {{ transaction.remarks }}

    </td>

    <!-- Actions -->
    <td class="p-2">

        <button
            @click="$emit('delete')"
            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
        >
            Delete
        </button>

    </td>

</tr>

<Teleport to="body">

    <div
        v-if="
            tooltipVisible &&
            hoveredTransaction
        "
        class="
            fixed
            z-[99999]
            bg-slate-900
            text-white
            text-xs
            rounded-lg
            shadow-xl
            p-2
            min-w-[180px]
            pointer-events-none
        "
        :style="{
            left: tooltipPosition.x + 'px',
            top: tooltipPosition.y + 'px'
        }"
    >

        <div
            class="
                font-semibold
                border-b
                border-slate-700
                pb-2
                mb-2
            "
        >
            Bundle Stock
        </div>

        <div
            v-for="
                bundle
                in
                hoveredTransaction.item.bundle_sizes
            "
            :key="bundle.id"
            class="
                flex
                justify-between
                gap-2
                py-1
            "
        >
            <span>
                {{ bundle.bundle_size }}/Bundle
            </span>

            <span>
                {{ bundle.available_bundles }}
                Bundles
            </span>
        </div>

    </div>

</Teleport>


</template>
