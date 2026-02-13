<template>
  <div
    :class="[
      'bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-200',
      hoverClasses,
      paddingClasses,
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  hover: {
    type: Boolean,
    default: false,
  },
  padding: {
    type: String,
    default: 'normal',
    validator: (value) => ['none', 'small', 'normal', 'large'].includes(value),
  },
})

const hoverClasses = computed(() => {
  return props.hover ? 'hover:shadow-md hover:scale-[1.02] cursor-pointer' : ''
})

const paddingClasses = computed(() => {
  const paddingMap = {
    none: '',
    small: 'p-4',
    normal: 'p-6',
    large: 'p-8',
  }
  return paddingMap[props.padding]
})
</script>
