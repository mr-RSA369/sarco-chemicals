<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import SmartItemForm from '@/Components/Forms/SmartItemForm.vue'

const showModal = ref(true)
</script>

<template>
    <AppLayout>
    <SmartItemForm
        :show="showModal"
        :category-id="3"
        category-name="Chemical"
        @close="showModal = false"
    />
    </AppLayout>
</template>
