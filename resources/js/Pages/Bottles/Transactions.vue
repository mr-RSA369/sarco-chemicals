<script setup>
import TransactionGrid from '@/Components/Inventory/TransactionGrid.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
    <AppLayout>
    <TransactionGrid
        :category-id="1"
        category-name="Bottles"
    />
    </AppLayout>
</template>
