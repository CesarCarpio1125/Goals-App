<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { useGoals } from '@/composables/useGoals'
import { useIcons } from '@/composables/useIcons'
import { Head } from '@inertiajs/vue3'

// Layouts
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Organisms
import GoalsOverview from '@/Components/Organisms/GoalsOverview.vue'

// Molecules
import StatsGrid from '@/Components/Molecules/StatsGrid.vue'
import QuickActions from '@/Components/Molecules/QuickActions.vue'

const page = usePage()
const { getIcon } = useIcons()

// Get stats from page props with safe defaults
const stats = computed(() => page.props.stats || {
  total: 0,
  completed: 0,
  in_progress: 0,
  pending: 0,
  completion_rate: 0
})

// Get goals from page props
const goals = computed(() => {
  console.log('Goals from props:', page.props.goals || [])
  console.log('Debug info from props:', page.props.debug_info || [])
  console.log('Stats from props:', page.props.stats || {})
  return page.props.goals || []
})

// Get motivational message from backend
const motivationalMessage = computed(() => page.props.motivationalMessage || 'Ready to crush your goals today!')

// Get streak from backend
const streak = computed(() => page.props.streak || 0)

// Initialize goals composable
const {
  loading,
  error,
  createGoal,
  updateGoal,
  deleteGoal,
  clearError,
} = useGoals()

const userName = computed(() => page.props.auth?.user?.name || 'User')
const hasMore = computed(() => false) // No pagination on dashboard

// Reactive states for buttons
const isCreateGoalHovered = ref(false)
const isCreateGoalLoading = ref(false)
const isRefreshing = ref(false)

const handleCreateGoal = () => {
  console.log('Create Goal clicked')
  console.log('User authenticated:', !!page.props.auth?.user)
  console.log('Route goals.create:', route('goals.create'))
  
  // Set loading state
  isCreateGoalLoading.value = true
  
  // Navigate to create goal page using Inertia
  router.visit(route('goals.create'))
}

const handleCreateGoalHover = () => {
  isCreateGoalHovered.value = true
  // Preload the route by prefetching
  router.prefetch(route('goals.create'))
}

const handleCreateGoalLeave = () => {
  isCreateGoalHovered.value = false
}

const handleViewGoals = () => {
  console.log('View All Goals clicked')
  router.visit(route('goals.index'))
}

const handleRefreshDashboard = async () => {
  isRefreshing.value = true
  try {
    await router.reload({ only: ['goals', 'stats'] })
  } finally {
    isRefreshing.value = false
  }
}

const handleViewProgress = () => {
  console.log('View progress analytics')
  // Navigate to analytics page
}

const handleViewCalendar = () => {
  console.log('View calendar')
  // Navigate to calendar view
}

const handleExportData = () => {
  console.log('Export data')
  // Trigger data export
}

const handleSelectGoal = (goal) => {
  console.log('Selected goal:', goal)
  router.visit(route('goals.show', goal.id))
}

const handleEditGoal = (goal) => {
  console.log('Edit goal:', goal)
  router.visit(route('goals.edit', goal.id))
}

const handleDeleteGoal = async (goal) => {
  if (confirm(`Are you sure you want to delete "${goal.title}"?`)) {
    try {
      await deleteGoal(goal.id)
      await handleRefreshDashboard()
    } catch (error) {
      console.error('Failed to delete goal:', error)
    }
  }
}

const handleGoalCreated = () => {
  handleRefreshDashboard()
}

// Auto-refresh on mount to ensure fresh data
onMounted(() => {
  console.log('Dashboard mounted, refreshing data...')
  handleRefreshDashboard()
})
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <!-- Welcome Section -->
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
      <!-- Dynamic Welcome Card -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
          <div class="mb-6 lg:mb-0">
            <h1 class="text-3xl font-bold mb-2">
              Welcome back, {{ userName }}! 👋
            </h1>
            <p class="text-blue-100 text-lg mb-4">
              {{ motivationalMessage }}
            </p>
            <div class="flex flex-wrap gap-4">
              <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                <div class="w-5 h-5" v-html="getIcon('fire')" />
                <span class="font-medium">{{ streak }} day streak</span>
              </div>
              <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                <div class="w-5 h-5" v-html="getIcon('target')" />
                <span class="font-medium">{{ stats.completed }} goals completed</span>
              </div>
              <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                <div class="w-5 h-5" v-html="getIcon('trending')" />
                <span class="font-medium">{{ Math.round(stats.completion_rate || 0) }}% completion rate</span>
              </div>
            </div>
          </div>
          
          <div class="flex flex-col space-y-3">
            <button
              @click="handleCreateGoal"
              @mouseenter="handleCreateGoalHover"
              @mouseleave="handleCreateGoalLeave"
              :disabled="isCreateGoalLoading"
              class="relative overflow-hidden bg-white text-blue-600 hover:bg-blue-50 font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
              <!-- Loading overlay -->
              <div 
                v-if="isCreateGoalLoading || isCreateGoalHovered"
                class="absolute inset-0 bg-blue-50 opacity-20 transition-opacity duration-200"
              />
              
              <!-- Loading spinner -->
              <div 
                v-if="isCreateGoalLoading"
                class="absolute inset-0 flex items-center justify-center"
              >
                <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin" />
              </div>
              
              <!-- Button content -->
              <div class="flex items-center space-x-2 relative z-10">
                <div 
                  class="w-5 h-5 transition-transform duration-200"
                  :class="{ 'rotate-90': isCreateGoalHovered }"
                  v-html="getIcon('plus')"
                />
                <span class="transition-colors duration-200">
                  {{ isCreateGoalLoading ? 'Loading...' : 'Create New Goal' }}
                </span>
              </div>
              
              <!-- Hover indicator -->
              <div 
                v-if="isCreateGoalHovered && !isCreateGoalLoading"
                class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full animate-pulse"
              />
            </button>
            <button
              @click="handleViewGoals"
              class="bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 font-semibold py-3 px-6 rounded-lg transition-all duration-200 border border-white/30"
            >
              <div class="flex items-center space-x-2">
                <div class="w-5 h-5" v-html="getIcon('list')" />
                <span>View All Goals ({{ stats.total }})</span>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <StatsGrid :stats="stats" />

      <!-- Quick Actions and Recent Goals -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
          <QuickActions
            @create-goal="handleCreateGoal"
            @view-goals="handleViewGoals"
            @view-progress="handleViewProgress"
            @view-calendar="handleViewCalendar"
            @export-data="handleExportData"
          />
        </div>

        <!-- Goals Overview -->
        <div class="lg:col-span-2">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Recent Goals</h2>
            <button
              @click="handleRefreshDashboard"
              :disabled="isRefreshing"
              class="flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 transition-colors"
            >
              <div 
                class="w-4 h-4"
                :class="{ 'animate-spin': isRefreshing }"
                v-html="getIcon('refresh')"
              />
              <span>{{ isRefreshing ? 'Refreshing...' : 'Refresh' }}</span>
            </button>
          </div>
          <GoalsOverview
            :goals="goals"
            :has-more="hasMore"
            :stats="stats"
            :loading="loading"
            @select-goal="handleSelectGoal"
            @edit-goal="handleEditGoal"
            @delete-goal="handleDeleteGoal"
            @create-goal="handleCreateGoal"
            @goal-created="handleGoalCreated"
          />
        </div>
      </div>

      <!-- Error State -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-2 text-sm text-red-700">
              {{ error }}
            </div>
            <div class="mt-4">
              <button
                @click="clearError()"
                class="text-sm font-medium text-red-600 hover:text-red-500 underline"
              >
                Dismiss
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
