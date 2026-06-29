<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SmartItemForm from '@/Components/Forms/SmartItemForm.vue'

const showModal = ref(true)
</script>

<template>
    <AppLayout>
        <div class="overflow:scroll">
    <SmartItemForm
        :show="showModal"
        :category-id="2"
        category-name="Packing Material"
        @close="showModal = false"
    />
    </div>
    </AppLayout>
</template>
