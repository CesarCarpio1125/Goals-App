<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { useGoals } from '@/composables/useGoals';
import { calculateTaskProgress } from '@/utils/taskProgress.js';

const page = usePage();
const { hasGoals, completedGoals, inProgressGoals, pendingGoals, completionRate } = useGoals();

// Get goals from page props
const goals = page.props.goals || [];
const stats = page.props.stats || {};

// Debug logging
console.log('=== GOALS INDEX DEBUG ===');
console.log('Goals from props:', goals);
console.log('Goals length:', goals.length);
console.log('Stats from props:', stats);
console.log('Stats keys:', stats ? Object.keys(stats) : 'No stats');
console.log('First goal tasks:', goals[0]?.tasks);
console.log('First goal tasks length:', goals[0]?.tasks?.length);
console.log('First goal completed tasks:', goals[0]?.tasks?.filter(t => t.completed)?.length);

// Log each goal's progress
goals.forEach((goal, index) => {
    const progress = calculateTaskProgress(goal);
    console.log(`Goal ${index + 1} (${goal.title}):`, progress);
});

// Hover and loading states
const isCreateGoalHovered = ref(false);
const isCreateGoalLoading = ref(false);

// Refresh data when page becomes visible (user returns from goal details)
onMounted(() => {
    const checkForUpdates = () => {
        // Check if we came from goal details with updates
        const goalUpdated = sessionStorage.getItem('goal_updated');
        if (goalUpdated === 'true') {
            // Clear the flag
            sessionStorage.removeItem('goal_updated');
            // Reload the page to get updated goal data
            router.reload({ only: ['goals', 'stats'] });
        }
    };
    
    const handleVisibilityChange = () => {
        if (document.visibilityState === 'visible') {
            checkForUpdates();
        }
    };
    
    // Listen for visibility changes
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    // Also check on focus
    const handleFocus = () => {
        checkForUpdates();
    };
    
    window.addEventListener('focus', handleFocus);
    
    // Check immediately on mount
    checkForUpdates();
    
    // Cleanup
    return () => {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
        window.removeEventListener('focus', handleFocus);
    };
});

const handleCreateGoalHover = () => {
    isCreateGoalHovered.value = true;
    // Preload the route by prefetching
    router.prefetch(route('goals.create'));
};

const handleCreateGoalLeave = () => {
    isCreateGoalHovered.value = false;
};

const handleCreateGoalClick = () => {
    isCreateGoalLoading.value = true;
    router.visit(route('goals.create'));
};

// Debug logging
console.log('=== GOALS INDEX DEBUG ===');
console.log('Page props:', page.props);
console.log('Goals from props:', goals);
console.log('Goals length:', goals.length);
console.log('Goals array:', Array.isArray(goals) ? goals : 'Not an array');
console.log('Stats from props:', stats);
console.log('Stats keys:', stats ? Object.keys(stats) : 'No stats');
console.log('Flash messages:', page.props.flash);

// Check if goals exist but not showing
if (goals.length > 0) {
    console.log('Goals found but maybe not showing:');
    goals.forEach((goal, index) => {
        console.log(`Goal ${index}:`, {
            id: goal.id,
            title: goal.title,
            status: goal.status,
            user_id: goal.user_id
        });
    });
} else {
    console.log('No goals found in props');
}
</script>

<template>
    <Head title="My Goals" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Goals</h1>
                    <p class="mt-2 text-gray-600">Track and manage your personal goals</p>
                </div>
                <div class="flex space-x-3">
                    <!-- Dashboard Button -->
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200"
                        title="Go to Dashboard"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </Link>
                    
                    <!-- Refresh Button -->
                    <button
                        @click="router.reload({ only: ['goals', 'stats'] })"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200"
                        title="Refresh goals and stats"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    
                    <!-- Create Goal Button -->
                    <button
                        @click="handleCreateGoalClick"
                        @mouseenter="handleCreateGoalHover"
                        @mouseleave="handleCreateGoalLeave"
                        :disabled="isCreateGoalLoading"
                        class="relative overflow-hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                    >
                    <!-- Loading overlay -->
                    <div 
                        v-if="isCreateGoalLoading || isCreateGoalHovered"
                        class="absolute inset-0 bg-blue-700 opacity-20 transition-opacity duration-200"
                    />
                    
                    <!-- Loading spinner -->
                    <div 
                        v-if="isCreateGoalLoading"
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    </div>
                    
                    <!-- Button content -->
                    <div class="flex items-center space-x-2 relative z-10">
                        <svg 
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-90': isCreateGoalHovered }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="transition-colors duration-200">
                            {{ isCreateGoalLoading ? 'Loading...' : 'Create New Goal' }}
                        </span>
                    </div>
                    
                    <!-- Hover indicator -->
                    <div 
                        v-if="isCreateGoalHovered && !isCreateGoalLoading"
                        class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"
                    />
                </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Goals</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ stats.total || 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ stats.completed || 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">In Progress</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ stats.in_progress || 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goals List -->
            <div v-if="goals.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    <li v-for="goal in goals" :key="goal.id">
                        <Link :href="route('goals.show', goal.hash || goal.id)" class="block hover:bg-gray-50 transition-colors duration-150">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900 hover:text-blue-600 transition-colors">{{ goal.title }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ goal.description }}</p>
                                        <div class="mt-2 flex items-center text-sm text-gray-500">
                                            <span>Progress: {{ calculateTaskProgress(goal).completedTasks }} / {{ calculateTaskProgress(goal).totalTasks }} tasks</span>
                                            <div class="ml-4 flex-1 bg-gray-200 rounded-full h-2">
                                                <div 
                                                    class="bg-blue-600 h-2 rounded-full" 
                                                    :style="{ width: calculateTaskProgress(goal).progressPercentage + '%' }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span 
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': calculateTaskProgress(goal).status === 'completed',
                                                'bg-yellow-100 text-yellow-800': calculateTaskProgress(goal).status === 'in_progress',
                                                'bg-gray-100 text-gray-800': calculateTaskProgress(goal).status === 'pending'
                                            }"
                                        >
                                            {{ calculateTaskProgress(goal).status.charAt(0).toUpperCase() + calculateTaskProgress(goal).status.slice(1).replace('_', ' ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No goals yet</h3>
                <p class="text-gray-500 mb-6">Get started by creating your first goal.</p>
                <button
                    @click="handleCreateGoalClick"
                    @mouseenter="handleCreateGoalHover"
                    @mouseleave="handleCreateGoalLeave"
                    :disabled="isCreateGoalLoading"
                    class="relative overflow-hidden inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                >
                    <!-- Loading overlay -->
                    <div 
                        v-if="isCreateGoalLoading || isCreateGoalHovered"
                        class="absolute inset-0 bg-blue-700 opacity-20 transition-opacity duration-200"
                    />
                    
                    <!-- Loading spinner -->
                    <div 
                        v-if="isCreateGoalLoading"
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    </div>
                    
                    <!-- Button content -->
                    <div class="flex items-center space-x-2 relative z-10">
                        <svg 
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-90': isCreateGoalHovered }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="transition-colors duration-200">
                            {{ isCreateGoalLoading ? 'Loading...' : 'Create Your First Goal' }}
                        </span>
                    </div>
                    
                    <!-- Hover indicator -->
                    <div 
                        v-if="isCreateGoalHovered && !isCreateGoalLoading"
                        class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"
                    />
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
