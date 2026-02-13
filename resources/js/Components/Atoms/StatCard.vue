<template>
  <div class="text-center">
    <div
      class="inline-flex items-center justify-center w-12 h-12 rounded-lg mb-3"
      :class="iconBackgroundClass"
    >
      <div class="w-6 h-6" :class="iconColorClass" v-html="iconSvg" />
    </div>
    <div class="text-2xl font-bold text-gray-900 mb-1">
      {{ value }}
    </div>
    <div class="text-sm text-gray-600">
      {{ label }}
    </div>
    <div
      v-if="trend"
      class="inline-flex items-center mt-2 text-xs font-medium"
      :class="trendColorClass"
    >
      <div class="w-3 h-3 mr-1" v-html="trendIconSvg" />
      {{ trendText }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useIcons } from '@/composables/useIcons'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  value: {
    type: [String, Number],
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: true,
  },
  trend: {
    type: String,
    default: null,
    validator: (value) => ['up', 'down', 'neutral'].includes(value),
  },
  trendValue: {
    type: [String, Number],
    default: null,
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple'].includes(value),
  },
})

const { getIcon, getTrendIcon } = useIcons()
const { getColorClasses } = useTheme()

const iconSvg = computed(() => getIcon(props.icon))
const trendIconSvg = computed(() => getTrendIcon(props.trend))

const colorClasses = computed(() => 
  getColorClasses(props.color, ['50', 'text'])
)

const iconBackgroundClass = computed(() => colorClasses.value[0])
const iconColorClass = computed(() => colorClasses.value[1])

const trendColorClass = computed(() => {
  const colorMap = {
    up: 'text-green-600',
    down: 'text-red-600',
    neutral: 'text-gray-600',
  }
  return colorMap[props.trend] || colorMap.neutral
})

const trendText = computed(() => {
  if (!props.trendValue) return ''
  return `${props.trendValue}%`
})
</script>
