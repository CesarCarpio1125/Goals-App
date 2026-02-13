<template>
  <div class="space-y-6">
    <!-- Calendar Header -->
    <div class="flex items-center justify-between">
      <CalendarNavigation
        :current-month="currentMonth"
        :disable-previous="!canGoToPrevious"
        :disable-next="!canGoToNext"
        @previous="goToPreviousMonth"
        @next="goToNextMonth"
      />
      
      <div class="flex items-center space-x-2">
        <button
          @click="exportToPdf"
          class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors flex items-center space-x-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Export PDF</span>
        </button>
        <button
          @click="goToToday"
          class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
        >
          Today
        </button>
        <button
          @click="toggleView"
          class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
        >
          {{ viewMode === 'month' ? 'Week' : 'Month' }}
        </button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <CalendarGrid
      :current-month="currentMonth"
      :selected-date="selectedDate"
      :events="calendarEvents"
      @select-day="handleSelectDay"
    />

    <!-- Selected Date Events -->
    <div v-if="selectedDate" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <CalendarEventList
          :selected-date="selectedDate"
          :events="selectedDateEvents"
          @event-action="handleEventAction"
        />
      </div>
      
      <!-- Quick Stats -->
      <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Total Events</span>
              <span class="text-sm font-medium text-gray-900">{{ selectedDateEvents.length }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Goal Deadlines</span>
              <span class="text-sm font-medium text-red-600">
                {{ goalDeadlinesCount }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Completed Goals</span>
              <span class="text-sm font-medium text-green-600">
                {{ completedGoalsCount }}
              </span>
            </div>
          </div>
        </div>

        <!-- Mini Calendar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Navigation</h3>
          <div class="grid grid-cols-3 gap-2 text-center">
            <button
              v-for="month in quickNavMonths"
              :key="month.value"
              @click="handleGoToMonth(month.year, month.index)"
              class="p-2 text-xs rounded hover:bg-gray-100 transition-colors"
              :class="{ 'bg-blue-100 text-blue-700': month.isCurrent }"
            >
              {{ month.label }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useCalendar } from '@/composables/useCalendar.js'

// Components
import CalendarNavigation from '../Atoms/CalendarNavigation.vue'
import CalendarGrid from '../Molecules/CalendarGrid.vue'
import CalendarEventList from '../Molecules/CalendarEventList.vue'

const props = defineProps({
  goals: {
    type: Array,
    default: () => []
  },
  initialDate: {
    type: Date,
    default: () => new Date()
  }
})

// Use calendar composable
const {
  currentMonth,
  selectedDate,
  canGoToPrevious,
  canGoToNext,
  goToPreviousMonth,
  goToNextMonth,
  goToToday,
  selectDate,
  goToMonth,
  generateEventsFromGoals,
  getEventsForSelectedDate,
  getCurrentMonthName,
  getCurrentYear
} = useCalendar(props.initialDate)

// Local state
const viewMode = ref('month')

// Generate calendar events from goals
const calendarEvents = computed(() => {
  return generateEventsFromGoals(props.goals)
})

// Get events for selected date
const selectedDateEvents = computed(() => {
  return getEventsForSelectedDate(calendarEvents.value)
})

// Count specific event types
const goalDeadlinesCount = computed(() => {
  return selectedDateEvents.value.filter(event => event.type === 'goal-deadline').length
})

const completedGoalsCount = computed(() => {
  return selectedDateEvents.value.filter(event => event.type === 'goal-completed').length
})

// Quick navigation months
const quickNavMonths = computed(() => {
  const months = []
  const currentYear = getCurrentYear()
  const currentMonthIndex = currentMonth.value.getMonth()
  
  // Show 3 months before and after current month
  for (let i = -3; i <= 3; i++) {
    const monthIndex = currentMonthIndex + i
    let adjustedIndex = monthIndex
    let year = currentYear
    
    if (monthIndex < 0) {
      adjustedIndex = monthIndex + 12
      year = currentYear - 1
    } else if (monthIndex > 11) {
      adjustedIndex = monthIndex - 12
      year = currentYear + 1
    }
    
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    
    months.push({
      label: monthNames[adjustedIndex],
      index: adjustedIndex,
      year,
      value: `${year}-${adjustedIndex}`,
      isCurrent: i === 0
    })
  }
  
  return months
})

// Event handlers
const handleSelectDay = (date) => {
  selectDate(date)
}

const handleEventAction = (event) => {
  if (event.actionData) {
    if (event.type.includes('goal')) {
      // Navigate to goal details
      router.visit(route('goals.show', event.actionData.hash || event.actionData.id))
    } else if (event.type.includes('task')) {
      // Navigate to goal with task focus
      router.visit(route('goals.show', event.actionData.goal.hash || event.actionData.goal.id))
    }
  }
}

const toggleView = () => {
  viewMode.value = viewMode.value === 'month' ? 'week' : 'month'
  // TODO: Implement week view
}

const exportToPdf = () => {
  const month = currentMonth.value.getMonth() + 1 // JS months are 0-indexed
  const year = currentMonth.value.getFullYear()
  
  // Trigger download by navigating to the export URL
  window.location.href = route('calendar.export.pdf', { month, year })
}

const handleGoToMonth = (year, monthIndex) => {
  goToMonth(year, monthIndex)
}
</script>
