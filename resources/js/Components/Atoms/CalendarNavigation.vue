<template>
  <div class="flex items-center justify-between mb-6">
    <button
      @click="$emit('previous')"
      class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
      :disabled="disablePrevious"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <div class="text-center">
      <h2 class="text-xl font-bold text-gray-900">{{ monthYear }}</h2>
      <p class="text-sm text-gray-500">{{ weekRange }}</p>
    </div>

    <button
      @click="$emit('next')"
      class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
      :disabled="disableNext"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentMonth: {
    type: Date,
    required: true
  },
  disablePrevious: {
    type: Boolean,
    default: false
  },
  disableNext: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['previous', 'next'])

const monthYear = computed(() => {
  const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ]
  return `${months[props.currentMonth.getMonth()]} ${props.currentMonth.getFullYear()}`
})

const weekRange = computed(() => {
  const firstDay = new Date(props.currentMonth.getFullYear(), props.currentMonth.getMonth(), 1)
  const lastDay = new Date(props.currentMonth.getFullYear(), props.currentMonth.getMonth() + 1, 0)
  
  const startWeek = getWeekNumber(firstDay)
  const endWeek = getWeekNumber(lastDay)
  
  if (startWeek === endWeek) {
    return `Week ${startWeek}`
  }
  return `Weeks ${startWeek}-${endWeek}`
})

const getWeekNumber = (date) => {
  const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
  const dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1))
  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7)
}
</script>
