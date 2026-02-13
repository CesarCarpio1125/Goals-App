<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Weekday Headers -->
    <div class="grid grid-cols-7 border-b border-gray-200">
      <div
        v-for="day in weekDays"
        :key="day"
        class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
      >
        {{ day }}
      </div>
    </div>

    <!-- Calendar Days Grid -->
    <div class="grid grid-cols-7">
      <CalendarDay
        v-for="day in calendarDays"
        :key="day.key"
        :date="day.date"
        :events="day.events"
        :is-current-month="day.isCurrentMonth"
        :is-today="day.isToday"
        :is-selected="day.isSelected"
        @select="$emit('select-day', day.date)"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import CalendarDay from '../Atoms/CalendarDay.vue'

const props = defineProps({
  currentMonth: {
    type: Date,
    required: true
  },
  selectedDate: {
    type: Date,
    default: null
  },
  events: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['select-day'])

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const calendarDays = computed(() => {
  const days = []
  const year = props.currentMonth.getFullYear()
  const month = props.currentMonth.getMonth()
  
  // Get first day of month and how many days to show from previous month
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())
  
  // Generate 42 days (6 weeks)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  for (let i = 0; i < 42; i++) {
    const currentDate = new Date(startDate)
    currentDate.setDate(startDate.getDate() + i)
    
    const isCurrentMonth = currentDate.getMonth() === month
    const isToday = currentDate.toDateString() === today.toDateString()
    const isSelected = props.selectedDate && currentDate.toDateString() === props.selectedDate.toDateString()
    
    // Get events for this date
    const dayEvents = props.events.filter(event => {
      const eventDate = new Date(event.date)
      eventDate.setHours(0, 0, 0, 0)
      currentDate.setHours(0, 0, 0, 0)
      return eventDate.toDateString() === currentDate.toDateString()
    })
    
    days.push({
      key: `${currentDate.getFullYear()}-${currentDate.getMonth()}-${currentDate.getDate()}`,
      date: currentDate,
      isCurrentMonth,
      isToday,
      isSelected,
      events: dayEvents
    })
  }
  
  return days
})
</script>
