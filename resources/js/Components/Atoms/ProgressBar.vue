<template>
  <div class="w-full">
    <div class="flex justify-between items-center mb-2" v-if="showLabel">
      <span class="text-sm font-medium text-gray-700">{{ label }}</span>
      <span class="text-sm font-semibold" :class="textColorClass">{{ safePercentage }}%</span>
    </div>
    <div
      class="w-full bg-gray-200 rounded-full h-3 overflow-hidden"
      :class="sizeClasses"
    >
      <div
        class="h-full bg-blue-600 rounded-full transition-all duration-500 ease-out"
        :style="{ width: `${safePercentage}%` }"
      >
        <div
          v-if="showIcon && safePercentage > 10"
          class="w-2 h-2 bg-white rounded-full animate-pulse"
        />
      </div>
    </div>
    <div v-if="showDetails" class="flex justify-between mt-2 text-xs text-gray-500">
      <span>{{ current }} {{ unit }}</span>
      <span>{{ target }} {{ unit }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  percentage: {
    type: Number,
    default: 0,
    validator: (value) => value >= 0 && value <= 100,
  },
  current: {
    type: Number,
    default: 0,
  },
  target: {
    type: Number,
    default: 0,
  },
  label: {
    type: String,
    default: '',
  },
  unit: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  showLabel: {
    type: Boolean,
    default: true,
  },
  showDetails: {
    type: Boolean,
    default: false,
  },
  showIcon: {
    type: Boolean,
    default: true,
  },
})

const { getProgressColor } = useTheme()

const safePercentage = computed(() => {
  const result = Math.max(0, Math.min(props.percentage || 0, 100))
  console.log(`ProgressBar - Received: ${props.percentage}, Safe: ${result}`)
  return result
})

const progressColorClass = computed(() => {
  const color = getProgressColor(safePercentage.value)
  return color[600] // Use the 600 shade for progress bar
})

const textColorClass = computed(() => {
  const color = getProgressColor(safePercentage.value)
  return color.text // Use the text color for percentage
})

const sizeClasses = computed(() => {
  const sizeMap = {
    small: 'h-2',
    medium: 'h-3',
    large: 'h-4',
  }
  return sizeMap[props.size]
})

const widthClasses = computed(() => {
  if (safePercentage.value === 0) return 'w-0'
  if (safePercentage.value === 100) return 'w-full'
  return ''
})
</script>
