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
const showProgressModal = ref(false)

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
  showProgressModal.value = true
}

const closeProgressModal = () => {
  showProgressModal.value = false
}

const handleViewCalendar = () => {
  console.log('View calendar')
  // Navigate to calendar page
  router.visit(route('calendar'))
}

const handleExportData = () => {
  console.log('Export data')
  // Trigger dashboard PDF export
  window.location.href = route('dashboard.export.pdf')
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

      <!-- Progress Analytics Modal -->
      <div v-if="showProgressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeProgressModal">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white" @click.stop>
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Progress Analytics</h3>
            <button @click="closeProgressModal" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Content -->
          <div class="space-y-6">
            <!-- Overall Stats -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-4">Overall Performance</h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600">{{ stats.total }}</div>
                  <div class="text-sm text-gray-600">Total Goals</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-green-600">{{ stats.completed }}</div>
                  <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-yellow-600">{{ stats.in_progress }}</div>
                  <div class="text-sm text-gray-600">In Progress</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-600">{{ Math.round(stats.completion_rate || 0) }}%</div>
                  <div class="text-sm text-gray-600">Success Rate</div>
                </div>
              </div>
            </div>

            <!-- Progress Visualization -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-4">Progress Distribution</h4>
              <div class="space-y-4">
                <div>
                  <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                    <span>Completed Goals</span>
                    <span>{{ stats.total > 0 ? Math.round((stats.completed / stats.total) * 100) : 0 }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-green-500 h-3 rounded-full transition-all duration-500"
                      :style="{ width: (stats.total > 0 ? (stats.completed / stats.total) * 100 : 0) + '%' }"
                    ></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                    <span>In Progress</span>
                    <span>{{ stats.total > 0 ? Math.round((stats.in_progress / stats.total) * 100) : 0 }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-yellow-500 h-3 rounded-full transition-all duration-500"
                      :style="{ width: (stats.total > 0 ? (stats.in_progress / stats.total) * 100 : 0) + '%' }"
                    ></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                    <span>Pending</span>
                    <span>{{ stats.total > 0 ? Math.round((stats.pending / stats.total) * 100) : 0 }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-gray-400 h-3 rounded-full transition-all duration-500"
                      :style="{ width: (stats.total > 0 ? (stats.pending / stats.total) * 100 : 0) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Goals Progress -->
            <div v-if="goals.length > 0" class="bg-gray-50 rounded-lg p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-4">Recent Goals Progress</h4>
              <div class="space-y-3">
                <div v-for="goal in goals.slice(0, 5)" :key="goal.id" class="flex items-center justify-between p-3 bg-white rounded-lg">
                  <div class="flex-1">
                    <h5 class="font-medium text-gray-900">{{ goal.title }}</h5>
                    <div class="mt-1 flex items-center text-sm text-gray-500">
                      <span>Progress: {{ goal.tasks?.filter(t => t.completed).length || 0 }} / {{ goal.tasks?.length || 0 }} tasks</span>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <div class="w-16 bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-blue-600 h-2 rounded-full" 
                        :style="{ width: (goal.tasks?.length > 0 ? (goal.tasks.filter(t => t.completed).length / goal.tasks.length) * 100 : 0) + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 w-12 text-right">
                      {{ goal.tasks?.length > 0 ? Math.round((goal.tasks.filter(t => t.completed).length / goal.tasks.length) * 100) : 0 }}%
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Motivational Insights -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-4">Insights & Recommendations</h4>
              <div class="space-y-3">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h5 class="text-sm font-medium text-green-800">Performance Analysis</h5>
                    <p class="text-sm text-green-700 mt-1">
                      {{ stats.completion_rate >= 70 ? 'Excellent progress! You\'re crushing your goals!' : 
                         stats.completion_rate >= 40 ? 'Good progress! Keep up the momentum!' : 
                         'You\'re just getting started. Every journey begins with a single step!' }}
                    </p>
                  </div>
                </div>
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h5 class="text-sm font-medium text-blue-800">Recommendation</h5>
                    <p class="text-sm text-blue-700 mt-1">
                      {{ stats.in_progress > stats.completed ? 'Focus on completing existing goals before starting new ones.' :
                         stats.pending > 0 ? 'Break down pending goals into smaller, manageable tasks.' :
                         'Consider setting more challenging goals to push yourself further.' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              @click="closeProgressModal"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
            >
              Close
            </button>
            <button
              @click="handleViewGoals"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
              View All Goals
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
