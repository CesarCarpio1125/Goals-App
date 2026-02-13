<template>
  <div
    :class="dayClasses"
    @click="$emit('select', date)"
    class="relative p-2 text-center cursor-pointer transition-all duration-200 hover:bg-blue-50"
  >
    <div class="text-sm font-medium">{{ dayNumber }}</div>
    <div v-if="hasEvents" class="mt-1">
      <div class="flex justify-center space-x-1">
        <div
          v-for="(event, index) in events.slice(0, 3)"
          :key="index"
          :class="getEventColor(event.type)"
          class="w-1.5 h-1.5 rounded-full"
        ></div>
      </div>
    </div>
    <div v-if="isToday" class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full"></div>
    <div v-if="isGoalDeadline" class="absolute bottom-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  date: {
    type: Date,
    required: true
  },
  events: {
    type: Array,
    default: () => []
  },
  isCurrentMonth: {
    type: Boolean,
    default: true
  },
  isToday: {
    type: Boolean,
    default: false
  },
  isSelected: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select'])

const dayNumber = computed(() => props.date.getDate())

const hasEvents = computed(() => props.events.length > 0)

const isGoalDeadline = computed(() => 
  props.events.some(event => event.type === 'goal-deadline')
)

const dayClasses = computed(() => {
  const classes = []
  
  if (!props.isCurrentMonth) {
    classes.push('text-gray-400')
  } else {
    classes.push('text-gray-900')
  }
  
  if (props.isToday) {
    classes.push('bg-blue-100', 'font-bold')
  }
  
  if (props.isSelected) {
    classes.push('ring-2', 'ring-blue-500', 'bg-blue-50')
  }
  
  if (hasEvents.value) {
    classes.push('font-semibold')
  }
  
  return classes
})

const getEventColor = (type) => {
  const colors = {
    'goal-deadline': 'bg-red-500',
    'goal-created': 'bg-blue-500',
    'goal-completed': 'bg-green-500',
    'task-due': 'bg-yellow-500',
    'milestone': 'bg-purple-500'
  }
  return colors[type] || 'bg-gray-500'
}
</script>
