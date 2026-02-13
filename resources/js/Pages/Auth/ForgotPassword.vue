<script setup>
import AuthForm from '@/Components/Organisms/AuthForm.vue';
import ThemedInput from '@/Components/Atoms/ThemedInput.vue';
import ThemedButton from '@/Components/Atoms/ThemedButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useIcons } from '@/composables/useIcons';

defineProps({
    status: {
        type: String,
    },
});

const { getIcon } = useIcons();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />
    
    <AuthForm
        title="Forgot Password?"
        subtitle="No problem. Just let us know your email address and we'll email you a password reset link."
        submit-text="Email Password Reset Link"
        color="blue"
        :is-processing="form.processing"
        link-text="Remember your password?"
        link-action="Sign in"
        link-route="login"
        @submit="submit"
    >
        <template #fields>
            <!-- Email field -->
            <ThemedInput
                id="email"
                type="email"
                label="Email Address"
                placeholder="Enter your email address"
                v-model="form.email"
                :error="form.errors.email"
                required
                autofocus
                autocomplete="username"
            />

            <!-- Success message -->
            <div
                v-if="status"
                class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl"
            >
                <div class="flex items-center">
                    <div class="w-5 h-5 text-green-600 mr-3" v-html="getIcon('check')" />
                    <p class="text-sm font-medium text-green-800">
                        {{ status }}
                    </p>
                </div>
            </div>
        </template>
    </AuthForm>
</template>
