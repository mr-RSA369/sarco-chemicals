<script setup>
import StockGrid from '@/Components/Inventory/StockGrid.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
<AppLayout>
<StockGrid
    :category-id="1"
    category-name="Bottles"
/>
</AppLayout>
</template>
