<template>
  <AuthForm
    title="Welcome Back"
    subtitle="Sign in to continue your journey"
    submit-text="Sign In"
    color="blue"
    :is-processing="isLoginProcessing"
    link-text="Don't have an account?"
    link-action="Sign up"
    :link-route="route('register')"
    @submit="handleSubmit"
  >
    <template #fields>
      <!-- Status message -->
      <div
        v-if="status"
        class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"
      >
        {{ status }}
      </div>

      <!-- Email field -->
      <ThemedInput
        id="email"
        v-model="loginForm.email"
        label="Email Address"
        type="email"
        placeholder="Enter your email"
        :error="loginErrors.email"
        hint="We'll never share your email with anyone else"
        required
        autofocus
        autocomplete="username"
        color="blue"
      />

      <!-- Password field -->
      <ThemedInput
        id="password"
        v-model="loginForm.password"
        label="Password"
        type="password"
        placeholder="Enter your password"
        :error="loginErrors.password"
        hint="Use a strong password for security"
        required
        autocomplete="current-password"
        color="blue"
        show-password-toggle
      />

      <!-- Remember me -->
      <div class="flex items-center">
        <input
          id="remember"
          v-model="loginForm.remember"
          type="checkbox"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label for="remember" class="ml-2 block text-sm text-gray-700">
          Remember me
        </label>
      </div>

      <!-- Forgot password link -->
      <div class="text-right">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-blue-600 hover:text-blue-500 transition-colors duration-200"
        >
          Forgot your password?
        </Link>
      </div>
    </template>
  </AuthForm>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ThemedInput from '../Atoms/ThemedInput.vue'
import AuthForm from './AuthForm.vue'
import { useAuth } from '@/composables/useAuth'

const props = defineProps({
  status: {
    type: String,
    default: null,
  },
  canResetPassword: {
    type: Boolean,
    default: false,
  },
})

const { loginForm, login, isLoginProcessing, loginErrors } = useAuth()

const handleSubmit = () => {
  login()
}
</script>
