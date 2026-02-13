<template>
  <div class="space-y-6">
    <!-- Header with filters and stats -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
      <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">
          Recent Goals
        </h2>
        <p class="text-sm text-gray-600">
          Showing {{ Math.min(goals.length, 6) }} of {{ stats?.total || 0 }} total goals
        </p>
      </div>
      <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
        <button
          v-for="filter in filters"
          :key="filter.key"
          @click="activeFilter = filter.key"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
            activeFilter === filter.key
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Goals grid with enhanced cards -->
    <div v-if="filteredGoals.length > 0" class="space-y-4">
      <!-- Featured goal (most recent or highest progress) -->
      <div v-if="filteredGoals[0]" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Featured
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getGoalStatusClass(filteredGoals[0])"
              >
                {{ getGoalStatusText(filteredGoals[0]) }}
              </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ filteredGoals[0].title }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ filteredGoals[0].description || 'No description' }}</p>
            <div class="flex items-center space-x-4">
              <div class="flex-1">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                  <span>Progress</span>
                  <span class="font-medium">{{ getGoalPercentage(filteredGoals[0]) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-300"
                    :style="{ width: getGoalPercentage(filteredGoals[0]) + '%' }"
                  />
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="$emit('select-goal', filteredGoals[0])"
                  class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                >
                  <div class="w-4 h-4" v-html="icons.eye" />
                </button>
                <button
                  @click="$emit('edit-goal', filteredGoals[0])"
                  class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  <div class="w-4 h-4" v-html="icons.edit" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Rest of goals in grid -->
      <div v-if="filteredGoals.length > 1" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <GoalCard
          v-for="goal in filteredGoals.slice(1)"
          :key="goal.id"
          :goal="goal"
          @select="$emit('select-goal', goal)"
          @edit="$emit('edit-goal', goal)"
          @delete="$emit('delete-goal', goal)"
        />
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center py-12">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
        <div class="w-8 h-8 text-gray-400" v-html="icons.target" />
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        {{ emptyStateTitle }}
      </h3>
      <p class="text-gray-600 mb-6">
        {{ emptyStateMessage }}
      </p>
      <button
        @click="$emit('create-goal')"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
      >
        <div class="w-5 h-5 mr-2" v-html="icons.plus" />
        Create Your First Goal
      </button>
    </div>

    <!-- Load more -->
    <div v-if="hasMore" class="text-center">
      <button
        @click="$emit('load-more')"
        class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
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

const emit = defineEmits(['select-goal', 'edit-goal', 'delete-goal', 'create-goal', 'load-more'])

const activeFilter = ref('all')

const filters = [
  { key: 'all', label: 'All Goals' },
  { key: 'in_progress', label: 'In Progress' },
  { key: 'completed', label: 'Completed' },
  { key: 'overdue', label: 'Overdue' },
]

const icons = computed(() => ({
  target: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
  plus: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>',
  arrow: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>',
  eye: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
  edit: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
}))

const filteredGoals = computed(() => {
  if (activeFilter.value === 'all') return props.goals

  return props.goals.filter(goal => {
    const progress = (goal.current_value / goal.target_value) * 100
    
    switch (activeFilter.value) {
      case 'in_progress':
        return progress < 100
      case 'completed':
        return progress >= 100
      case 'overdue':
        return goal.deadline && new Date(goal.deadline) < new Date() && progress < 100
      default:
        return true
    }
  })
})

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
      return 'Great job! You don\'t have any overdue goals.'
    default:
      return 'Start tracking your progress by creating your first goal.'
  }
})

const getGoalStatusClass = (goal) => {
  const progress = (goal.current_value / goal.target_value) * 100
  
  if (progress >= 100) {
    return 'bg-green-100 text-green-800'
  } else if (goal.deadline && new Date(goal.deadline) < new Date()) {
    return 'bg-red-100 text-red-800'
  } else {
    return 'bg-yellow-100 text-yellow-800'
  }
}

const getGoalStatusText = (goal) => {
  const progress = (goal.current_value / goal.target_value) * 100
  
  if (progress >= 100) {
    return 'Completed'
  } else if (goal.deadline && new Date(goal.deadline) < new Date()) {
    return 'Overdue'
  } else {
    return 'In Progress'
  }
}

const getGoalPercentage = (goal) => {
  const progress = calculateTaskProgress(goal)
  console.log(`GoalsOverview - ${goal.title}: ${progress.completedTasks}/${progress.totalTasks} = ${progress.progressPercentage}%`)
  return progress.progressPercentage
}
</script>
