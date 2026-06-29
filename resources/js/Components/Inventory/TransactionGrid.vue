<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

import TransactionRow from './TransactionRow.vue'
import AddTransactionRow from './AddTransactionRow.vue'

const props = defineProps({
    categoryId: {
        type: Number,
        required: true
    },

    categoryName: {
        type: String,
        required: true
    }
})

const loading = ref(false)

const transactions = ref([])

const currentPage = ref(1)

const perPage = ref(50)

const totalPages = ref(1)

const showAddRow = ref(false)

const loadTransactions = async (page = 1) => {
    try {

        loading.value = true

        const response = await axios.get(
            '/api/transactions',
            {
                params: {
                    category_id: props.categoryId,
                    page,
                    per_page: perPage.value
                }
            }
        )

        transactions.value =
            response.data.data

        currentPage.value =
            response.data.current_page

        totalPages.value =
            response.data.last_page

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

const deleteTransaction = async (id) => {

    if (
        !confirm(
            'Delete transaction?'
        )
    ) {
        return
    }

    try {

        await axios.delete(
            `/api/transactions/${id}`
        )

        await loadTransactions(
            currentPage.value
        )

    } catch (error) {

        alert(
            error.response?.data?.message
            ??
            'Delete failed'
        )
    }
}

const saved = async () => {

    showAddRow.value = false

    await loadTransactions(
        currentPage.value
    )
}

onMounted(() => {
    loadTransactions()
})
</script>

<template>

<div class="p-6 max-h-[60vh]">

    <div
        class="
        flex
        flex-row
        md:flex-row
        md:items-center
        md:justify-between
        gap-4
        mb-4
        "
    >

        <h1
            class="text-xl sm:text-lg font-semibold"
        >
            {{ categoryName }} Transactions
        </h1>

        <div
            class="flex flex-row gap-2 "
        >

            <select
                v-model="perPage"
                @change="loadTransactions()"
                class="border rounded py-2 sm:w-auto"
            >
                <option :value="50">50</option>
                <option :value="75">75</option>
                <option :value="100">100</option>
                <option :value="200">200</option>
                <option :value="300">300</option>
            </select>

            <button
                @click="showAddRow = true"
                class="bg-emerald-600 text-white px-4 py-2 rounded"
            >
                Add Transaction
            </button>

        </div>

    </div>

    <div
        class="overflow-auto max-h-[60vh] bg-white rounded-lg shadow w-full"
    >

        <table
            class="min-w-[1000px] w-full text-sm overflow-auto"
        >

            <thead class="sticky top-0 bg-gray-100 z-10">

            <tr
                class="bg-gray-100"
            >

                <th class="p-2">
                    Date
                </th>

                <th class="p-2">
                    Product
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-2"
                >
                    Batch
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-2"
                >
                    MFG
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-2"
                >
                    EXP
                </th>

                <th
                    v-if="categoryId === 2"
                    class="p-2"
                >
                    Rack
                </th>

                <th class="p-2">
                    OB
                </th>

                <th class="p-2">
                    Type
                </th>

                <th class="p-2">
                    Bundle Size
                </th>

                <th class="p-2">
                    Bundles
                </th>

                <th class="p-2">
                    Qty
                </th>

                <th class="p-2">
                    CB
                </th>

                <th class="p-2">
                    Remarks
                </th>

                <th class="p-2">
                    Actions
                </th>

            </tr>

            </thead>

            <tbody class="max-h-[90vh] overflow-y-auto">

            <AddTransactionRow
                v-if="showAddRow"
                :category-id="categoryId"
                @saved="saved"
                @cancel="showAddRow = false"
            />

            <TransactionRow
                v-for="transaction in transactions"
                :key="transaction.id"
                :transaction="transaction"
                :category-id="categoryId"
                @delete="
                    deleteTransaction(
                        transaction.id
                    )
                "
            />

            </tbody>

        </table>

    </div>

    <div
        class="flex justify-center gap-2 mt-4"
    >

        <button
            @click="
                loadTransactions(
                    currentPage - 1
                )
            "
            :disabled="
                currentPage === 1
            "
            class="border px-3 py-1 rounded"
        >
            Previous
        </button>

        <span>
            Page {{ currentPage }}
            /
            {{ totalPages }}
        </span>

        <button
            @click="
                loadTransactions(
                    currentPage + 1
                )
            "
            :disabled="
                currentPage === totalPages
            "
            class="border px-3 py-1 rounded"
        >
            Next
        </button>

    </div>

</div>

</template>
