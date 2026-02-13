import { ref, computed } from 'vue'

export function useCalendar(initialDate = new Date()) {
  const currentMonth = ref(new Date(initialDate.getFullYear(), initialDate.getMonth(), 1))
  const selectedDate = ref(initialDate)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed properties
  const today = computed(() => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    return today
  })

  const canGoToPrevious = computed(() => {
    const minDate = new Date(2020, 0, 1) // January 2020
    return currentMonth.value > minDate
  })

  const canGoToNext = computed(() => {
    const maxDate = new Date(2030, 11, 31) // December 2030
    const nextMonth = new Date(currentMonth.value)
    nextMonth.setMonth(nextMonth.getMonth() + 1)
    return nextMonth <= maxDate
  })

  // Methods
  const goToPreviousMonth = () => {
    if (!canGoToPrevious.value) return
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1)
  }

  const goToNextMonth = () => {
    if (!canGoToNext.value) return
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1)
  }

  const goToToday = () => {
    const today = new Date()
    currentMonth.value = new Date(today.getFullYear(), today.getMonth(), 1)
    selectedDate.value = today
  }

  const selectDate = (date) => {
    selectedDate.value = new Date(date)
  }

  const goToMonth = (year, month) => {
    currentMonth.value = new Date(year, month, 1)
  }

  const goToYear = (year) => {
    currentMonth.value = new Date(year, currentMonth.value.getMonth(), 1)
  }

  // Generate calendar events from goals
  const generateEventsFromGoals = (goals) => {
    const events = []
    
    goals.forEach(goal => {
      // Goal created event
      if (goal.created_at) {
        events.push({
          id: `goal-created-${goal.id}`,
          type: 'goal-created',
          title: `Goal Created: ${goal.title}`,
          description: goal.description,
          date: goal.created_at,
          actionable: true,
          actionText: 'View Goal',
          actionData: goal
        })
      }

      // Goal deadline event
      if (goal.deadline) {
        events.push({
          id: `goal-deadline-${goal.id}`,
          type: 'goal-deadline',
          title: `Deadline: ${goal.title}`,
          description: `Goal deadline approaching`,
          date: goal.deadline,
          actionable: true,
          actionText: 'View Goal',
          actionData: goal
        })
      }

      // Goal completed event
      if (goal.status === 'completed' && goal.updated_at) {
        events.push({
          id: `goal-completed-${goal.id}`,
          type: 'goal-completed',
          title: `Completed: ${goal.title}`,
          description: 'Congratulations! Goal achieved!',
          date: goal.updated_at,
          actionable: true,
          actionText: 'View Goal',
          actionData: goal
        })
      }

      // Task due events
      if (goal.tasks && goal.tasks.length > 0) {
        goal.tasks.forEach(task => {
          if (task.due_date) {
            events.push({
              id: `task-due-${task.id}`,
              type: 'task-due',
              title: `Task Due: ${task.title}`,
              description: `Task for goal: ${goal.title}`,
              date: task.due_date,
              actionable: true,
              actionText: 'View Task',
              actionData: { goal, task }
            })
          }
        })
      }
    })

    return events
  }

  // Get events for a specific date range
  const getEventsForDateRange = (events, startDate, endDate) => {
    return events.filter(event => {
      const eventDate = new Date(event.date)
      return eventDate >= startDate && eventDate <= endDate
    })
  }

  // Get events for selected date
  const getEventsForSelectedDate = (events) => {
    if (!selectedDate.value) return []
    
    return events.filter(event => {
      const eventDate = new Date(event.date)
      eventDate.setHours(0, 0, 0, 0)
      const selected = new Date(selectedDate.value)
      selected.setHours(0, 0, 0, 0)
      return eventDate.toDateString() === selected.toDateString()
    })
  }

  // Navigation helpers
  const getCurrentMonthName = () => {
    const months = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ]
    return months[currentMonth.value.getMonth()]
  }

  const getCurrentYear = () => {
    return currentMonth.value.getFullYear()
  }

  const getMonthYearString = () => {
    return `${getCurrentMonthName()} ${getCurrentYear()}`
  }

  // Reset error
  const clearError = () => {
    error.value = null
  }

  return {
    // State
    currentMonth,
    selectedDate,
    isLoading,
    error,
    today,
    canGoToPrevious,
    canGoToNext,

    // Methods
    goToPreviousMonth,
    goToNextMonth,
    goToToday,
    selectDate,
    goToMonth,
    goToYear,
    generateEventsFromGoals,
    getEventsForDateRange,
    getEventsForSelectedDate,
    getCurrentMonthName,
    getCurrentYear,
    getMonthYearString,
    clearError
  }
}
