// Progress calculation utilities
export const PROGRESS_THRESHOLDS = {
  EXCELLENT: 75,
  GOOD: 50,
  AVERAGE: 25,
  POOR: 0,
}

export const GOAL_STATUS = {
  COMPLETED: 'completed',
  IN_PROGRESS: 'in_progress',
  OVERDUE: 'overdue',
  NOT_STARTED: 'not_started',
}

// Calculate progress percentage safely
export const calculateProgress = (current, target) => {
  if (!target || target === 0) return 0
  const percentage = (current / target) * 100
  return Math.min(Math.round(percentage), 100) // Cap at 100%
}

// Determine goal status based on progress and deadline
export const getGoalStatus = (current, target, deadline) => {
  const progress = calculateProgress(current, target)
  
  if (progress >= 100) return GOAL_STATUS.COMPLETED
  
  if (deadline && new Date(deadline) < new Date()) {
    return GOAL_STATUS.OVERDUE
  }
  
  if (progress === 0) return GOAL_STATUS.NOT_STARTED
  
  return GOAL_STATUS.IN_PROGRESS
}

// Get progress level for styling
export const getProgressLevel = (percentage) => {
  if (percentage >= PROGRESS_THRESHOLDS.EXCELLENT) return 'excellent'
  if (percentage >= PROGRESS_THRESHOLDS.GOOD) return 'good'
  if (percentage >= PROGRESS_THRESHOLDS.AVERAGE) return 'average'
  return 'poor'
}

// Format deadline for display
export const formatDeadline = (deadline) => {
  if (!deadline) return 'No deadline'
  
  const date = new Date(deadline)
  const now = new Date()
  const isPast = date < now
  
  return {
    formatted: date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    }),
    isPast,
    isToday: date.toDateString() === now.toDateString(),
    daysUntil: Math.ceil((date - now) / (1000 * 60 * 60 * 24)),
  }
}

// Calculate completion rate from stats
export const calculateCompletionRate = (completed, total) => {
  if (!total || total === 0) return 0
  return Math.round((completed / total) * 100)
}

// Get motivational message based on progress
export const getMotivationalMessage = (stats, progress) => {
  const completionRate = stats.completion_rate || 0
  
  // Edge cases
  if (stats.total === 0) {
    return "Every great journey begins with a single step."
  }
  
  if (stats.completed === stats.total && stats.total > 0) {
    return "Incredible! You've completed all your goals. Time to set new challenges!"
  }
  
  // Progress-based messages
  if (completionRate >= 75) {
    return "You're crushing it! Just a little more effort to reach your goals."
  }
  
  if (completionRate >= 50) {
    return "Great progress! You're halfway there. Keep the momentum going!"
  }
  
  if (completionRate >= 25) {
    return "Good start! You're building momentum. Every step counts!"
  }
  
  return "You're just getting started. Small consistent actions lead to big results!"
}

// Get motivational title based on progress
export const getMotivationalTitle = (progress) => {
  if (progress >= 100) return 'Goal Crusher!'
  if (progress >= 75) return 'Almost There!'
  if (progress >= 50) return 'Making Progress!'
  if (progress >= 25) return 'Getting Started!'
  return 'Begin Your Journey!'
}

// Get greeting based on time of day
export const getGreeting = () => {
  const hour = new Date().getHours()
  
  if (hour < 12) return 'Ready to crush your goals this morning?'
  if (hour < 17) return 'Keep up the great work this afternoon!'
  return 'Time to reflect on today\'s progress!'
}
