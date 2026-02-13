import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { createLoginForm, createRegisterForm, createPasswordResetForm, createResetPasswordForm, createAuthActions, createAuthComputed } from '@/utils/authUtils'

export function useAuth() {
  // Create forms using factory functions
  const forms = {
    login: createLoginForm(),
    register: createRegisterForm(),
    passwordReset: createPasswordResetForm(),
    resetPassword: createResetPasswordForm(),
  }

  // Create actions using factory function
  const actions = createAuthActions(forms)

  // Create computed properties using factory function
  const computedProps = createAuthComputed(forms)

  return {
    // Forms
    loginForm: forms.login,
    registerForm: forms.register,
    passwordForm: forms.passwordReset,
    resetForm: forms.resetPassword,
    
    // Actions
    ...actions,
    
    // Computed properties
    ...computedProps,
    
    // Validation helpers (kept as they are useful)
    validateEmail: (email) => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    },
    
    validatePassword: (password) => {
      return password.length >= 8
    },
    
    validatePasswordMatch: (password, confirmation) => {
      return password === confirmation && password.length > 0
    },
  }
}
