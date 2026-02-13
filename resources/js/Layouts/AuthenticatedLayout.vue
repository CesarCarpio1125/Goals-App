<script setup>
import DynamicHeader from '@/Components/Organisms/DynamicHeader.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Dynamic Header -->
        <DynamicHeader />

        <!-- Page Content -->
        <main class="py-6">
            <slot />
        </main>
    </div>
</template>
