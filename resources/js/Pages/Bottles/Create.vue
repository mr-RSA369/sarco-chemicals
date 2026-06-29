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
        :category-id="1"
        category-name="Bottle Item"
        @close="showModal = false"
    />
    </AppLayout>
</template>
