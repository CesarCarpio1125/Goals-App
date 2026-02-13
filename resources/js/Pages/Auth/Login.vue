<script setup>
import DynamicLoginForm from '@/Components/Organisms/DynamicLoginForm.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Log in" />
    
    <DynamicLoginForm
        :can-reset-password="canResetPassword"
        :status="status"
    />
</template>
