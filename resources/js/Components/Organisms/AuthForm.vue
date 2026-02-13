<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden">
      <div
        class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br rounded-full opacity-20 blur-3xl animate-pulse"
        :class="backgroundGradient"
      />
      <div
        class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr rounded-full opacity-20 blur-3xl animate-pulse"
        :class="secondaryGradient"
        style="animation-delay: 1s"
      />
    </div>

    <!-- Form container -->
    <div class="relative w-full max-w-md">
      <!-- Logo/Brand section -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-lg" :class="logoBackground">
          <div class="w-8 h-8 text-white" v-html="getIcon('target')" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          {{ title }}
        </h1>
        <p class="text-gray-600">
          {{ subtitle }}
        </p>
      </div>

      <!-- Form -->
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200 p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <slot name="fields" />

          <!-- Submit button -->
          <ThemedButton
            type="submit"
            :text="submitText"
            icon="rocket"
            icon-position="right"
            :color="color"
            variant="solid"
            size="lg"
            :loading="isProcessing"
            :disabled="isProcessing"
            full-width
            @click="handleSubmit"
          />
        </form>

        <!-- Auth link -->
        <div class="mt-6 text-center">
          <span class="text-sm text-gray-600">
            {{ linkText }}
          </span>
          <Link
            :href="linkRoute"
            class="text-sm font-medium transition-colors duration-200 ml-1"
            :class="linkColor"
          >
            {{ linkAction }}
          </Link>
        </div>
      </div>

      <!-- Features preview -->
      <div v-if="showFeatures" class="mt-8 text-center">
        <div class="inline-flex items-center justify-center space-x-6 text-xs text-gray-500">
          <div class="flex items-center">
            <div class="w-4 h-4 mr-1" v-html="getIcon('check')" />
            <span>Free Forever</span>
          </div>
          <div class="flex items-center">
            <div class="w-4 h-4 mr-1" v-html="getIcon('check')" />
            <span>No Credit Card</span>
          </div>
          <div class="flex items-center">
            <div class="w-4 h-4 mr-1" v-html="getIcon('check')" />
            <span>Cancel Anytime</span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 text-center">
        <p class="text-xs text-gray-500">
          © 2026 Goals App. Built with ❤️ for achievers.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ThemedButton from '../Atoms/ThemedButton.vue'
import { useIcons } from '@/composables/useIcons'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    required: true,
  },
  submitText: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    default: 'blue',
  },
  isProcessing: {
    type: Boolean,
    default: false,
  },
  linkText: {
    type: String,
    required: true,
  },
  linkAction: {
    type: String,
    required: true,
  },
  linkRoute: {
    type: String,
    required: true,
  },
  showFeatures: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit'])

const { getIcon } = useIcons()

const handleSubmit = () => {
  emit('submit')
}

// Computed properties for dynamic styling
const backgroundGradient = computed(() => {
  const gradients = {
    blue: 'from-blue-400 to-purple-600',
    green: 'from-green-400 to-blue-600',
  }
  return gradients[props.color] || gradients.blue
})

const secondaryGradient = computed(() => {
  const gradients = {
    blue: 'from-purple-400 to-pink-600',
    green: 'from-blue-400 to-purple-600',
  }
  return gradients[props.color] || gradients.blue
})

const logoBackground = computed(() => {
  const backgrounds = {
    blue: 'bg-gradient-to-br from-blue-600 to-purple-600',
    green: 'bg-gradient-to-br from-green-600 to-blue-600',
  }
  return backgrounds[props.color] || backgrounds.blue
})

const linkColor = computed(() => {
  const colors = {
    blue: 'text-blue-600 hover:text-blue-500',
    green: 'text-green-600 hover:text-green-500',
  }
  return colors[props.color] || colors.blue
})
</script>
