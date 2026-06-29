<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    categoryId: Number,
    categoryName: String
})

const stocks = ref([])
const loading = ref(false)

const hoveredStock = ref(null)

const tooltipVisible = ref(false)

const tooltipPosition = ref({
    x: 0,
    y: 0
})

const showTooltip = (
    stock,
    event
) => {

    if (
        !stock.item?.bundle_sizes?.length
    ) {
        return
    }

    hoveredStock.value = stock

    tooltipPosition.value = {
        x: event.clientX - 48,
        y: event.clientY + 15
    }

    tooltipVisible.value = true
}

const hideTooltip = () => {

    tooltipVisible.value = false

    hoveredStock.value = null
}

const loadStock = async () => {

    try {

        loading.value = true

        const response = await axios.get(
            '/api/stock',
            {
                params: {
                    category_id: props.categoryId
                }
            }
        )

        stocks.value = response.data

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

onMounted(() => {
    loadStock()
})
</script>

<template>

<div class="p-6">

    <div class="mb-6
    flex
    flex-col
    sm:flex-row
    sm:items-center
    sm:justify-between
    gap-2
    "
>
        <h1
            class="text-2xl font-semibold"
        >
            {{ categoryName }} Stock
        </h1>

    </div>

    <div
        class=" max-h-[70vh] bg-white rounded-lg shadow overflow-auto"
    >

        <table
            class="w-full text-sm overflow-auto"
        >

            <thead class="sticky top-0 bg-gray-100 z-10">

            <tr
                class="bg-gray-100"
            >

                <th class="p-3 text-left">
                    Product
                </th>

                <th class="p-3 text-left">
                    Item
                </th>

                <th class="p-3 text-left">
                    Size
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-3 text-left"
                >
                    Batch
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-3 text-left"
                >
                    MFG
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-3 text-left"
                >
                    EXP
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-3 text-left"
                >
                    Rack
                </th>

                <th class="p-3 text-center">
                    Min Qty
                </th>

                <th class="p-3 text-center">
                    Current Stock
                </th>

            </tr>

            </thead>

            <tbody>

            <tr
                v-for="stock in stocks"
                :key="stock.id"
                class="border-b hover:bg-gray-50"
            >

                <td class="p-3">
                    {{ stock.item?.product_name }}
                </td>

                <td class="p-3">
                    {{ stock.item?.item_name }}
                </td>

                <td class="p-3">
                    {{ stock.item?.size }}
                    {{ stock.item?.size_unit }}
                </td>

                <template
                    v-if="categoryId === 2"
                >

                    <td class="p-3">
                        {{ stock.batch_no }}
                    </td>

                    <td class="p-3">
                        {{ stock.mfg_date }}
                    </td>

                    <td class="p-3">
                        {{ stock.exp_date }}
                    </td>

                    <td class="p-3">
                        {{ stock.rack_no }}
                    </td>

                </template>

                <td
                    class="p-3 text-center"
                >

                    <span
                        @mouseenter="
                            showTooltip(
                                stock,
                                $event
                            )
                        "
                        @mouseleave="
                            hideTooltip()
                        "
                        class="cursor-pointer"
                        :class="[
                            Number(stock.current_qty)
                            <
                            Number(stock.item?.minimum_qty)
                                ? 'text-red-600 font-bold'
                                : 'text-green-700 font-medium'
                        ]"
                    >
                        {{ stock.current_qty }}
                    </span>

                </td>

                <td
                    class="p-3 text-center"
                >
                    {{ stock.item?.minimum_qty }}
                </td>

            </tr>

            <tr
                v-if="!stocks.length"
            >
                <td
                    colspan="20"
                    class="p-6 text-center text-gray-500"
                >
                    No Stock Found
                </td>
            </tr>

            </tbody>

        </table>

    </div>

</div>

<Teleport to="body">

    <div
        v-if="
            tooltipVisible &&
            hoveredStock
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
            min-w-[200px]
            pointer-events-none
        "
        :style="{
            left:
        tooltipPosition.x + 'px'
        ,

            top:
                tooltipPosition.y + 'px'
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
                hoveredStock.item.bundle_sizes
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
                {{ bundle.bundle_size }}
                / Bundle
            </span>

            <span>
                {{ bundle.available_bundles }}
                Bundles
            </span>

        </div>

    </div>

</Teleport>

</template>
