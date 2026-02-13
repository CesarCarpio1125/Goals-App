<template>
  <AuthForm
    title="Join Goals App"
    subtitle="Start your journey to achieve your dreams"
    submit-text="Create Account"
    color="green"
    :is-processing="isRegisterProcessing"
    link-text="Already have an account?"
    link-action="Sign in"
    :link-route="route('login')"
    :show-features="true"
    @submit="handleSubmit"
  >
    <template #fields>
      <!-- Name field -->
      <ThemedInput
        id="name"
        v-model="registerForm.name"
        label="Full Name"
        type="text"
        placeholder="Enter your full name"
        :error="registerErrors.name"
        hint="This is how we'll address you in the app"
        required
        autofocus
        autocomplete="name"
        color="green"
      />

      <!-- Email field -->
      <ThemedInput
        id="email"
        v-model="registerForm.email"
        label="Email Address"
        type="email"
        placeholder="Enter your email"
        :error="registerErrors.email"
        hint="We'll never share your email with anyone else"
        required
        autocomplete="username"
        color="green"
      />

      <!-- Password field -->
      <ThemedInput
        id="password"
        v-model="registerForm.password"
        label="Password"
        type="password"
        placeholder="Create a strong password"
        :error="registerErrors.password"
        hint="Use at least 8 characters with letters and numbers"
        required
        autocomplete="new-password"
        color="green"
        show-password-toggle
      />

      <!-- Password confirmation field -->
      <ThemedInput
        id="password_confirmation"
        v-model="registerForm.password_confirmation"
        label="Confirm Password"
        type="password"
        placeholder="Re-enter your password"
        :error="registerErrors.password_confirmation"
        hint="Make sure it matches your password"
        required
        autocomplete="new-password"
        color="green"
        show-password-toggle
      />
    </template>
  </AuthForm>
</template>

<script setup>
import ThemedInput from '../Atoms/ThemedInput.vue'
import AuthForm from './AuthForm.vue'
import { useAuth } from '@/composables/useAuth'

const { registerForm, register, isRegisterProcessing, registerErrors } = useAuth()

const handleSubmit = () => {
  register()
}
</script>
