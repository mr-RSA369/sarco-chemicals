<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    categoryId: {
        type: Number,
        required: true
    }
})

const emit = defineEmits([
    'saved',
    'cancel'
])

const loading = ref(false)

const items = ref([])
const bundleSizes = ref([])

const form = reactive({
    transaction_date: new Date()
        .toISOString()
        .split('T')[0],

    item_id: '',

    batch_no: '',

    mfg_date: '',

    exp_date: '',

    rack_no: '',

    transaction_type: 'receipt',

    bundle_size: '',

    bundle_count: '',

    opening_balance: 0,

    closing_balance: 0,

    remarks: ''
})

const quantity = computed(() => {

    return (
        Number(form.bundle_size || 0)
        *
        Number(form.bundle_count || 0)
    )
})

const loadItems = async () => {

    const response =
        await axios.get(
            '/api/transactions/items',
            {
                params: {
                    category_id:
                        props.categoryId
                }
            }
        )

    items.value =
        response.data
}

const loadBundleSizes = async () => {

    if (!form.item_id)
        return

    try {

        const response =
            await axios.get(
                `/api/items/${form.item_id}/bundle-sizes`
            )

        bundleSizes.value =
            response.data

    } catch (error) {

        console.error(error)
    }
}

const lookupBatch = async () => {

    if (!form.item_id)
        return

    try {

        const response =
            await axios.get(
                '/api/transactions/batch-lookup',
                {
                    params: {
                        item_id:
                            form.item_id,

                        batch_no:
                            form.batch_no
                    }
                }
            )

        const data =
            response.data

        form.opening_balance =
            Number(
                data.opening_balance || 0
            )

        form.mfg_date =
            data.mfg_date || ''

        form.exp_date =
            data.exp_date || ''

        form.rack_no =
            data.rack_no || ''

        calculateClosing()

    } catch (error) {

        console.error(error)
    }
}

const calculateClosing = () => {

    if (
        form.transaction_type
        ===
        'receipt'
    ) {

        form.closing_balance =
            Number(form.opening_balance)
            +
            Number(quantity.value)

    } else {

        form.closing_balance =
            Number(form.opening_balance)
            -
            Number(quantity.value)
    }
}

const save = async () => {

    try {

        loading.value = true

        await axios.post(
            '/api/transactions',
            {
                item_id:
                    form.item_id,

                batch_no:
                    form.batch_no,

                mfg_date:
                    form.mfg_date,

                exp_date:
                    form.exp_date,

                rack_no:
                    form.rack_no,

                transaction_date:
                    form.transaction_date,

                transaction_type:
                    form.transaction_type,

                bundle_size:
                    form.bundle_size,

                bundle_count:
                    form.bundle_count,

                remarks:
                    form.remarks
            }
        )

        emit('saved')

    } catch (error) {

        alert(
            error.response?.data?.message
            ??
            'Save failed'
        )

    } finally {

        loading.value = false
    }
}

onMounted(() => {
    loadItems()
})
</script>

<template>

<tr class="bg-emerald-50">

    <!-- Date -->
    <td class="p-1">
        <input
            type="date"
            v-model="form.transaction_date"
            class="border rounded w-full px-2 py-1"
        >
    </td>

    <!-- Item -->
    <td class="p-1">

        <select
            v-model="form.item_id"
            @change="
                lookupBatch();
                loadBundleSizes();
            "
            class="border rounded w-full min-w-[120px] px-2 py-1"
        >
            <option value="">
                Select Item
            </option>

            <option
                v-for="item in items"
                :key="item.id"
                :value="item.id"
            >
                {{ item.item_name }}
                {{ item.size }}
                {{ item.size_unit }}
            </option>

        </select>

    </td>

    <!-- PM Only -->
    <template v-if="categoryId === 2">

        <td class="p-1">
            <input
                v-model="form.batch_no"
                @blur="lookupBatch"
                placeholder="Batch"
                class="border rounded w-full px-2 py-1"
            >
        </td>

        <td class="p-1">
            <input
                type="date"
                v-model="form.mfg_date"
                class="border rounded w-full px-2 py-1"
            >
        </td>

        <td class="p-1">
            <input
                type="date"
                v-model="form.exp_date"
                class="border rounded w-full px-2 py-1"
            >
        </td>

        <td class="p-1">
            <input
                v-model="form.rack_no"
                placeholder="Rack"
                class="border rounded w-full px-2 py-1"
            >
        </td>

    </template>

    <!-- Opening -->
    <td class="p-1">
        {{ form.opening_balance }}
    </td>

    <!-- Type -->
    <td class="p-1">

        <select
            v-model="form.transaction_type"
            @change="calculateClosing"
            class="border rounded w-full px-2 py-1"
        >
            <option value="receipt">
                Receipt
            </option>

            <option value="issue">
                Issue
            </option>
        </select>

    </td>

    <!-- Bundle Size -->
    <td class="p-1">

        <input
            v-if="form.transaction_type === 'receipt'"
            type="number"
            min="1"
            v-model="form.bundle_size"
            @input="calculateClosing"
            placeholder="Bundle Size"
            class="border rounded w-24 px-2 py-1"
        >

        <select
            v-else
            v-model="form.bundle_size"
            @change="calculateClosing"
            class="border rounded w-full px-2 py-1"
        >
            <option value="">
                Select
            </option>

            <option
                v-for="bundle in bundleSizes"
                :key="bundle.id"
                :value="bundle.bundle_size"
            >
                {{ bundle.bundle_size }}
                ({{ bundle.available_bundles }})
            </option>

        </select>

    </td>

    <!-- Bundle Count -->
    <td class="p-1">

        <input
            type="number"
            min="1"
            v-model="form.bundle_count"
            @input="calculateClosing"
            class="border rounded w-24 px-2 py-1"
        >

    </td>

    <!-- Quantity -->
    <td class="p-1 font-semibold">
        {{ quantity }}
    </td>

    <!-- Closing -->
    <td class="p-1">
        {{ form.closing_balance }}
    </td>

    <!-- Remarks -->
    <td class="p-1">

        <input
            v-model="form.remarks"
            class="border rounded w-full px-2 py-1"
        >

    </td>

    <!-- Actions -->
    <td class="p-1">

        <div class="flex gap-2">

            <button
                @click="save"
                :disabled="loading"
                class="bg-green-600 text-white px-3 py-1 rounded"
            >
                ✓
            </button>

            <button
                @click="$emit('cancel')"
                class="bg-red-600 text-white px-3 py-1 rounded"
            >
                ✕
            </button>

        </div>

    </td>

</tr>

</template>
