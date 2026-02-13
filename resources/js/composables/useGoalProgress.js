import { computed } from 'vue'

export function useGoalProgress(goal) {
  if (!goal) {
    return {
      totalTasks: 0,
      completedTasks: 0,
      pendingTasks: 0,
      progressPercentage: 0,
      status: 'pending',
      statusColor: 'gray',
      statusText: 'No Progress'
    }
  }

  const totalTasks = goal.tasks?.length || 0
  const completedTasks = goal.tasks?.filter(task => task.completed)?.length || 0
  const pendingTasks = totalTasks - completedTasks
  const progressPercentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0

  const status = computed(() => {
    if (totalTasks === 0) return 'pending'
    if (completedTasks === totalTasks) return 'completed'
    if (completedTasks > 0) return 'in_progress'
    return 'pending'
  })

  const statusColor = computed(() => {
    if (status.value === 'completed') return 'green'
    if (status.value === 'in_progress') return 'blue'
    return 'gray'
  })

  const statusText = computed(() => {
    switch (status.value) {
      case 'completed': return 'Completed'
      case 'in_progress': return 'In Progress'
      default: return 'Not Started'
    }
  })

  const isOverdue = computed(() => {
    return goal.deadline && new Date(goal.deadline) < new Date() && status.value !== 'completed'
  })

  return {
    // Raw data
    totalTasks,
    completedTasks,
    pendingTasks,
    progressPercentage,
    
    // Computed properties
    status,
    statusColor,
    statusText,
    isOverdue,
    
    // Helpers
    hasProgress: computed(() => progressPercentage > 0),
    isCompleted: computed(() => status.value === 'completed'),
    daysRemaining: computed(() => {
      if (!goal.deadline) return null
      const now = new Date()
      const deadline = new Date(goal.deadline)
      const diffTime = deadline - now
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      return Math.max(0, diffDays)
    })
  }
}
