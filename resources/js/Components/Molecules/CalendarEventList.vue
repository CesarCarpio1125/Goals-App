<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900">
        {{ selectedDateFormatted }}
      </h3>
      <span v-if="events.length > 0" class="text-sm text-gray-500">
        {{ events.length }} {{ events.length === 1 ? 'event' : 'events' }}
      </span>
    </div>

    <div v-if="events.length === 0" class="text-center py-8">
      <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <p class="text-gray-500">No events scheduled for this day</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="event in sortedEvents"
        :key="event.id"
        class="flex items-start space-x-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors"
      >
        <div class="flex-shrink-0">
          <div
            :class="getEventColor(event.type)"
            class="w-3 h-3 rounded-full mt-1"
          ></div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-medium text-gray-900 truncate">
            {{ event.title }}
          </h4>
          <p v-if="event.description" class="text-sm text-gray-500 mt-1">
            {{ event.description }}
          </p>
          <div class="flex items-center mt-2 space-x-4">
            <span v-if="event.time" class="text-xs text-gray-500">
              {{ event.time }}
            </span>
            <span class="text-xs text-gray-500">
              {{ getEventTypeLabel(event.type) }}
            </span>
          </div>
        </div>
        <div class="flex-shrink-0">
          <button
            v-if="event.actionable"
            @click="$emit('event-action', event)"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            {{ event.actionText || 'View' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  selectedDate: {
    type: Date,
    required: true
  },
  events: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['event-action'])

const selectedDateFormatted = computed(() => {
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  }
  return props.selectedDate.toLocaleDateString('en-US', options)
})

const sortedEvents = computed(() => {
  return [...props.events].sort((a, b) => {
    const timeA = a.time || '00:00'
    const timeB = b.time || '00:00'
    return timeA.localeCompare(timeB)
  })
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

const getEventTypeLabel = (type) => {
  const labels = {
    'goal-deadline': 'Goal Deadline',
    'goal-created': 'Goal Created',
    'goal-completed': 'Goal Completed',
    'task-due': 'Task Due',
    'milestone': 'Milestone'
  }
  return labels[type] || 'Event'
}
</script>
