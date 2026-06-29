<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import TransactionGrid from '@/Components/Inventory/TransactionGrid.vue'
</script>

<template>
    <AppLayout>
    <TransactionGrid
        :category-id="3"
        category-name="Chemicals"
    />
    </AppLayout>
</template>
