<template>
  <div class="space-y-6">
    <!-- Enhanced Header with filters and stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
      <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">
          Your Goals
        </h2>
        <p class="text-sm text-gray-600">
          Showing {{ filteredGoals.length }} of {{ props.stats?.total || 0 }} total goals
        </p>
      </div>
      <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
        <button
          v-for="filter in filters"
          :key="filter.key"
          @click="activeFilter = filter.key"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105',
            activeFilter === filter.key
              ? 'bg-blue-600 text-white shadow-lg'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Enhanced Featured Goal Section -->
    <div v-if="featuredGoal" class="mb-8">
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 shadow-lg relative overflow-hidden">
        <!-- Featured Badge -->
        <div class="absolute top-4 right-4">
          <span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full shadow-sm">
            <div class="w-3 h-3 mr-1" v-html="icons.sparkles" />
            Featured
          </span>
        </div>
        
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-3">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getGoalStatusClass(featuredGoal)"
              >
                {{ getGoalStatusText(featuredGoal) }}
              </span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ featuredGoal.title }}</h3>
            <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ featuredGoal.description || 'No description' }}</p>
            
            <!-- Progress Stats -->
            <div class="grid grid-cols-3 gap-4 mb-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ getGoalPercentage(featuredGoal) }}%</div>
                <div class="text-sm text-gray-500">Progress</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ useGoalProgress(featuredGoal).completedTasks }}</div>
                <div class="text-sm text-gray-500">Completed</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ useGoalProgress(featuredGoal).pendingTasks }}</div>
                <div class="text-sm text-gray-500">Remaining</div>
              </div>
            </div>
          </div>
          
          <!-- Progress Bar -->
          <div class="relative">
            <ProgressBar
              :percentage="getGoalPercentage(featuredGoal)"
              :current="useGoalProgress(featuredGoal).completedTasks"
              :target="useGoalProgress(featuredGoal).totalTasks"
              :show-details="true"
              :show-label="false"
              unit="tasks"
              size="medium"
            />
          </div>
          
          <!-- Action Buttons -->
          <div class="flex space-x-3 mt-4">
            <button
              @click="$emit('select-goal', featuredGoal)"
              class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
            >
              <div class="flex items-center justify-center">
                <div class="w-4 h-4 mr-2" v-html="icons.eye" />
                View Details
              </div>
            </button>
            <button
              @click="$emit('edit-goal', featuredGoal)"
              class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              title="Edit goal"
            >
              <div class="w-5 h-5" v-html="icons.edit" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Goals Grid -->
    <div v-if="otherGoals.length > 0" class="space-y-4">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Other Goals</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <GoalCard
          v-for="goal in otherGoals"
          :key="goal.id"
          :goal="goal"
          @select="$emit('select-goal', goal)"
          @edit="$emit('edit-goal', goal)"
          @delete="$emit('delete-goal', goal)"
        />
      </div>
    </div>

    <!-- Enhanced Empty State -->
    <div v-else class="text-center py-16">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
        <div class="w-10 h-10 text-gray-400" v-html="icons.target" />
      </div>
      <h3 class="text-xl font-semibold text-gray-900 mb-3">
        {{ emptyStateTitle }}
      </h3>
      <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
        {{ emptyStateMessage }}
      </p>
      <button
        @click="$emit('create-goal')"
        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-lg transform hover:scale-105"
      >
        <div class="w-5 h-5 mr-2" v-html="icons.plus" />
        Create Your First Goal
      </button>
    </div>

    <!-- Load More -->
    <div v-if="props.hasMore" class="text-center mt-8">
      <button
        @click="$emit('load-more')"
        class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium"
      >
        <div class="w-5 h-5 mr-2" v-html="icons.arrow" />
        Load More Goals
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import GoalCard from '../Molecules/GoalCard.vue'
import ProgressBar from '../Atoms/ProgressBar.vue'
import { useGoalProgress } from '@/composables/useGoalProgress.js'

const props = defineProps({
  goals: {
    type: Array,
    required: true,
    default: () => [],
  },
  hasMore: {
    type: Boolean,
    default: false,
  },
  stats: {
    type: Object,
    default: () => ({}),
  },
})

// Debug: verificar los datos que llegan
console.log('=== GOALS OVERVIEW DEBUG ===')
console.log('Goals props:', props.goals)
console.log('Goals length:', props.goals.length)
if (props.goals.length > 0) {
  console.log('First goal:', props.goals[0])
  console.log('First goal tasks:', props.goals[0].tasks)
  console.log('First goal tasks length:', props.goals[0].tasks?.length || 0)
}

const emit = defineEmits(['select-goal', 'edit-goal', 'delete-goal', 'create-goal', 'load-more'])

const activeFilter = ref('all')

const filters = [
  { key: 'all', label: 'All Goals' },
  { key: 'in_progress', label: 'In Progress' },
  { key: 'completed', label: 'Completed' },
  { key: 'overdue', label: 'Overdue' },
]

// Enhanced icons with better visual design
const icons = computed(() => ({
  target: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002 2m-6 9l2 2 4-4m6 2l2 2-4"/></svg>',
  plus: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>',
  arrow: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>',
  eye: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7z"/></svg>',
  edit: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
  sparkles: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 3h4M6 7v1m1 1h4M1 1h4"/></svg>',
  chart: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8v-6H3v6zm0 0V9a2 2 0 012 2h2a2 2 0 012 2v10m-6 0a2 2 0 00-2 2v6a2 2 0 002 2m0 0V5a2 2 0 012 2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
}))

// Enhanced filtered goals with better logic
const filteredGoals = computed(() => {
  if (activeFilter.value === 'all') return props.goals

  return props.goals.filter(goal => {
    const progress = useGoalProgress(goal)
    
    // Debug: log para verificar el estado
    console.log(`Goal: ${goal.title}`, {
      totalTasks: progress.totalTasks,
      completedTasks: progress.completedTasks,
      status: progress.status.value,
      filter: activeFilter.value
    })
    
    switch (activeFilter.value) {
      case 'in_progress':
        return progress.status.value === 'in_progress'
      case 'completed':
        return progress.status.value === 'completed'
      case 'overdue':
        return progress.isOverdue.value
      default:
        return true
    }
  })
})

// Enhanced empty states with better messaging
const emptyStateTitle = computed(() => {
  switch (activeFilter.value) {
    case 'in_progress':
      return 'No Active Goals'
    case 'completed':
      return 'No Completed Goals'
    case 'overdue':
      return 'No Overdue Goals'
    default:
      return 'No Goals Yet'
  }
})

const emptyStateMessage = computed(() => {
  switch (activeFilter.value) {
    case 'in_progress':
      return 'All your goals are completed or you haven\'t started any yet.'
    case 'completed':
      return 'You haven\'t completed any goals yet. Keep working!'
    case 'overdue':
      return 'Excellent! All your goals are on track. Keep up the great momentum!'
    default:
      return 'Start tracking your progress by creating your first goal.'
  }
})

// Featured goal logic - pick the most important one
const featuredGoal = computed(() => {
  if (filteredGoals.value.length === 0) return null
  
  // Priority: completed > in_progress > pending
  const completedGoals = filteredGoals.value.filter(g => useGoalProgress(g).status.value === 'completed')
  const inProgressGoals = filteredGoals.value.filter(g => useGoalProgress(g).status.value === 'in_progress')
  const pendingGoals = filteredGoals.value.filter(g => useGoalProgress(g).status.value === 'pending')
  
  // Return most recently updated goal from highest priority category
  if (completedGoals.length > 0) {
    return completedGoals.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0]
  }
  if (inProgressGoals.length > 0) {
    return inProgressGoals.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0]
  }
  if (pendingGoals.length > 0) {
    return pendingGoals.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0]
  }
  
  return filteredGoals.value[0]
})

// Other goals - exclude the featured goal to avoid duplication
const otherGoals = computed(() => {
  if (!featuredGoal.value) return filteredGoals.value
  
  return filteredGoals.value.filter(goal => goal.id !== featuredGoal.value.id)
})

// Enhanced status helpers
const getGoalStatusText = (goal) => {
  const progress = useGoalProgress(goal)
  return progress.statusText.value
}

const getGoalStatusClass = (goal) => {
  const progress = useGoalProgress(goal)
  const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
  const colorClasses = {
    completed: 'bg-green-100 text-green-800 border-green-200',
    in_progress: 'bg-blue-100 text-blue-800 border-blue-200',
    pending: 'bg-gray-100 text-gray-800 border-gray-200'
  }
  
  return `${baseClasses} ${colorClasses[progress.status.value] || colorClasses.pending}`
}

const getGoalPercentage = (goal) => {
  const progress = useGoalProgress(goal)
  return progress.progressPercentage
}
</script>
