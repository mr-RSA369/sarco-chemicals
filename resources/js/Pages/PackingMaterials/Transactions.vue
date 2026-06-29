<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import TransactionGrid from '@/Components/Inventory/TransactionGrid.vue'
</script>

<template>
    <AppLayout>
    <TransactionGrid
        :category-id="2"
        category-name="Packing Materials"
    />
    </AppLayout>
</template>
