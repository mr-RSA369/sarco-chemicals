<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import StockGrid from '@/Components/Inventory/StockGrid.vue'
</script>

<template>
<AppLayout>
<StockGrid
    :category-id="2"
    category-name="Packing Materials"
/>
</AppLayout>

</template>
