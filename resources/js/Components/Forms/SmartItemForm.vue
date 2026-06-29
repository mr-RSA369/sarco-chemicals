```vue
<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    show: Boolean,
    categoryId: Number,
    categoryName: String,
})

const emit = defineEmits([
    'close',
    'saved'
])

const loading = ref(false)
const loadingTypes = ref(false)
const types = ref([])

const form = reactive({
    category_id: props.categoryId,
    type_id: '',
    product_name: '',
    item_name: '',
    size: '',
    size_unit: 'ML',
    minimum_qty: 0,
})

const loadTypes = async () => {
    try {
        loadingTypes.value = true

        const response = await axios.get(
            `/api/item-types/${props.categoryId}`
        )

        types.value = response.data
    } catch (error) {
        console.error('Error loading types:', error)
    } finally {
        loadingTypes.value = false
    }
}

watch(
    () => props.categoryId,
    async (value) => {
        form.category_id = value
        form.type_id = ''

        await loadTypes()
    }
)

onMounted(() => {
    loadTypes()
})

const resetForm = () => {
    form.type_id = ''
    form.product_name = ''
    form.item_name = ''
    form.size = ''
    form.size_unit = 'ML'
    form.minimum_qty = 0
}

const submit = async () => {
    try {
        loading.value = true

        const response = await axios.post('/api/items', form)

        resetForm()

        emit('saved', response.data.message)
        emit('close')

    } catch (error) {
        console.error('Error saving item:', error)

        if (error.response?.data?.message) {
            alert(error.response.data.message)
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    >
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 mx-4 max-h-[90vh] flex
    flex-col"
        >
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold">
                    Add {{ categoryName }}
                </h2>

                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-red-500 text-xl"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <div class="grid md:grid-cols-2 px-2 overflow-y-auto gap-4">

                <!-- Type -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Type
                    </label>

                    <select
                        v-model="form.type_id"
                        class="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="">
                            {{
                                loadingTypes
                                    ? 'Loading Types...'
                                    : 'Select Type'
                            }}
                        </option>

                        <option
                            v-for="type in types"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>

                <!-- Product Name -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Product Name
                    </label>

                    <input
                        v-model="form.product_name"
                        type="text"
                        placeholder="Enter Product Name"
                        class="w-full border rounded-lg px-3 py-2"
                    />
                </div>

                <!-- Item Name -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Item Name
                    </label>

                    <input
                        v-model="form.item_name"
                        type="text"
                        placeholder="Enter Item Name"
                        class="w-full border rounded-lg px-3 py-2"
                    />
                </div>

                <!-- Size -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Size
                    </label>

                    <input
                        type="number"
                        v-model="form.size"
                        placeholder="Enter Size"
                        class="w-full border rounded-lg px-3 py-2"
                    />
                </div>

                <!-- Unit -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Unit
                    </label>

                    <select
                        v-model="form.size_unit"
                        class="w-full border rounded-lg px-3 py-2"
                    >
                        <option value="ML">ML</option>
                        <option value="GM">GM</option>
                        <option value="KG">KG</option>
                        <option value="LTR">LTR</option>
                    </select>
                </div>

                <!-- Minimum Quantity -->
                <div>
                    <label class="block mb-1 text-sm font-medium">
                        Minimum Quantity
                    </label>

                    <input
                        type="number"
                        v-model="form.minimum_qty"
                        placeholder="Enter Minimum Qty"
                        class="w-full border rounded-lg px-3 py-2"
                    />
                </div>

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 mt-8">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100"
                >
                    Cancel
                </button>

                <button
                    @click="submit"
                    :disabled="loading"
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50"
                >
                    {{ loading ? 'Saving...' : 'Save Item' }}
                </button>
            </div>
        </div>
    </div>
</template>
