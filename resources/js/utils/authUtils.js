// Utility functions for authentication
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

export const createAuthForm = (fields) => {
  return useForm(fields)
}

export const createLoginForm = () => {
  return createAuthForm({
    email: '',
    password: '',
    remember: false,
  })
}

export const createRegisterForm = () => {
  return createAuthForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  })
}

export const createPasswordResetForm = () => {
  return createAuthForm({
    email: '',
  })
}

export const createResetPasswordForm = () => {
  return createAuthForm({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
  })
}

export const createAuthActions = (forms) => {
  return {
    login: (options = {}) => {
      return forms.login.post(route('login'), {
        ...options,
        onFinish: () => forms.login.reset('password'),
      })
    },
    
    register: (options = {}) => {
      return forms.register.post(route('register'), {
        ...options,
        onFinish: () => forms.register.reset('password', 'password_confirmation'),
      })
    },
    
    requestPasswordReset: (options = {}) => {
      return forms.passwordReset.post(route('password.email'), options)
    },
    
    resetPassword: (options = {}) => {
      return forms.resetPassword.post(route('password.update'), options)
    },
    
    logout: (options = {}) => {
      return useForm().post(route('logout'), options)
    },
  }
}

export const createAuthComputed = (forms) => {
  return {
    isLoginProcessing: computed(() => forms.login.processing),
    isRegisterProcessing: computed(() => forms.register.processing),
    isPasswordProcessing: computed(() => forms.passwordReset.processing),
    isResetProcessing: computed(() => forms.resetPassword.processing),
    
    loginErrors: computed(() => forms.login.errors),
    registerErrors: computed(() => forms.register.errors),
    passwordErrors: computed(() => forms.passwordReset.errors),
    resetErrors: computed(() => forms.resetPassword.errors),
  }
}
