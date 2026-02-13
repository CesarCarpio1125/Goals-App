<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTaskProgress } from '@/composables/useTaskProgress.js';
import { useTaskManagement } from '@/composables/useTaskManagement.js';

const props = defineProps({
    goal: {
        type: Object,
        required: true
    }
});

// Get flash messages
const page = usePage();
const flash = computed(() => page.props.flash || {});

// Use composables for clean separation of concerns
const { tasks, newTaskTitle, isAddingTask, isLoading, motivationalMessage, recommendations, showFeedback, addTask, toggleTask, deleteTask } = useTaskManagement(props.goal.hash || props.goal.id, props.goal.tasks || []);
const { progressPercentage, taskProgress } = useTaskProgress(tasks);

const statusClasses = computed(() => {
    if (progressPercentage.value >= 100) return 'bg-green-100 text-green-800';
    if (props.goal.deadline && new Date(props.goal.deadline) < new Date()) {
        return 'bg-red-100 text-red-800';
    }
    return 'bg-blue-100 text-blue-800';
});

const statusText = computed(() => {
    if (progressPercentage.value >= 100) return 'Completed';
    if (props.goal.deadline && new Date(props.goal.deadline) < new Date()) {
        return 'Overdue';
    }
    return 'In Progress';
});

const formattedDeadline = computed(() => {
    if (!props.goal.deadline) return 'No deadline';
    const date = new Date(props.goal.deadline);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

const createdDate = computed(() => {
    const date = new Date(props.goal.created_at);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
});

const editGoal = () => {
    router.visit(route('goals.edit', props.goal.hash || props.goal.id));
};

const deleteGoal = () => {
    if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
        router.delete(route('goals.destroy', props.goal.hash || props.goal.id));
    }
};

const shareGoal = () => {
    const shareUrl = window.location.origin + route('goals.show', props.goal.hash || props.goal.id);

    if (navigator.share) {
        navigator.share({
            title: props.goal.title,
            text: props.goal.description,
            url: shareUrl
        });
    } else {
        navigator.clipboard.writeText(shareUrl);
        alert('Goal link copied to clipboard!');
    }
};

const showProgressModal = ref(false);

const openProgressModal = () => {
    showProgressModal.value = true;
};

const closeProgressModal = () => {
    showProgressModal.value = false;
};
</script>

<template>
    <Head :title="goal.title" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="mb-8">
                <!-- Flash Messages -->
                <div v-if="flash.success" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ flash.success }}
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <Link :href="route('goals.index')" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Goals
                        </Link>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button @click="editGoal" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Goal
                        </button>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-gray-900">{{ goal.title }}</h1>
                <p class="mt-2 text-gray-600">{{ goal.description }}</p>
            </div>

            <!-- Motivational Feedback -->
            <div v-if="showFeedback" class="mb-6 animate-fade-in">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-blue-800">{{ motivationalMessage }}</h3>
                            <div v-if="recommendations.length > 0" class="mt-2 text-sm text-blue-700">
                                <p class="font-medium">Recomendaciones:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li v-for="recommendation in recommendations" :key="recommendation">
                                        {{ recommendation }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Progress Overview -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Progress Overview</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Your progress is automatically calculated based on completed tasks</p>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <!-- Main Progress Bar -->
                                <div>
                                    <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                                        <span>Overall Progress</span>
                                        <span>{{ progressPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div
                                            class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                                            :style="{ width: progressPercentage + '%' }"
                                        ></div>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-500 mt-2">
                                        <span>{{ taskProgress.completed }} tasks completed</span>
                                        <span>{{ taskProgress.total }} total tasks</span>
                                    </div>
                                </div>

                                <!-- Stats Grid -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600">{{ taskProgress.total }}</div>
                                        <div class="text-sm text-gray-600">Total Tasks</div>
                                    </div>
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600">{{ taskProgress.completed }}</div>
                                        <div class="text-sm text-gray-600">Completed</div>
                                    </div>
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600">{{ taskProgress.pending }}</div>
                                        <div class="text-sm text-gray-600">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Section -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tasks</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Break down your goal into smaller tasks</p>
                                </div>
                                <button
                                    @click="isAddingTask = true"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Task
                                </button>
                            </div>
                        </div>

                        <!-- Add Task Form -->
                        <div v-if="isAddingTask" class="border-t border-gray-200 px-4 py-4 sm:px-6 bg-gray-50">
                            <div class="flex space-x-3">
                                <input
                                    v-model="newTaskTitle"
                                    @keyup.enter="addTask"
                                    @keyup.escape="isAddingTask = false"
                                    type="text"
                                    placeholder="What needs to be done?"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    ref="newTaskInput"
                                />
                                <button
                                    @click="addTask"
                                    :disabled="!newTaskTitle.trim() || isLoading"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Add
                                </button>
                                <button
                                    @click="isAddingTask = false; newTaskTitle = ''"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-200">
                            <div v-if="tasks.length === 0" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p>No tasks yet. Add your first task to get started!</p>
                            </div>

                            <ul v-else class="divide-y divide-gray-200">
                                <li v-for="task in tasks" :key="task.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :checked="task.completed"
                                            @change="toggleTask(task)"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span
                                            class="ml-3 flex-1"
                                            :class="{ 'line-through text-gray-500': task.completed }"
                                        >
                                            {{ task.title }}
                                        </span>
                                        <button
                                            @click="deleteTask(task.id)"
                                            class="ml-3 text-red-600 hover:text-red-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Goal Details -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Goal Details</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6 space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="statusClasses"
                                    >
                                        {{ statusText }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Target</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ goal.target_value }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Current Progress</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ goal.current_value || 0 }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formattedDeadline }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ createdDate }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6 space-y-3">
                            <button @click="openProgressModal" class="w-full inline-flex justify-center items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View Progress
                            </button>

                            <button @click="shareGoal" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 3.12-9.032 7.326m0 0A9.001 9.001 0 0012 21c4.474 0 8.268-3.12 9.032-7.326" />
                                </svg>
                                Share Goal
                            </button>

                            <button @click="deleteGoal" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Goal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Modal -->
            <div v-if="showProgressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeProgressModal">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Goal Progress Details</h3>
                        <button @click="closeProgressModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="space-y-6">
                        <!-- Overall Progress -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Overall Progress</h4>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                                        <span>Completion Rate</span>
                                        <span class="text-2xl font-bold text-blue-600">{{ progressPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div
                                            class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-500"
                                            :style="{ width: progressPercentage + '%' }"
                                        ></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div class="bg-white rounded-lg p-3">
                                        <div class="text-xl font-bold text-blue-600">{{ taskProgress.total }}</div>
                                        <div class="text-xs text-gray-600">Total Tasks</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3">
                                        <div class="text-xl font-bold text-green-600">{{ taskProgress.completed }}</div>
                                        <div class="text-xs text-gray-600">Completed</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3">
                                        <div class="text-xl font-bold text-yellow-600">{{ taskProgress.pending }}</div>
                                        <div class="text-xs text-gray-600">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Goal Metrics -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Goal Metrics</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Target Value</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ goal.target_value }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Current Value</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ goal.current_value || 0 }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Status</div>
                                    <div class="mt-1">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                            :class="statusClasses"
                                        >
                                            {{ statusText }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Deadline</div>
                                    <div class="text-sm text-gray-900">{{ formattedDeadline }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Motivational Message -->
                        <div v-if="showFeedback && motivationalMessage" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">{{ motivationalMessage }}</h3>
                                    <div v-if="recommendations.length > 0" class="mt-2 text-sm text-green-700">
                                        <p class="font-medium">Recommendations:</p>
                                        <ul class="list-disc list-inside mt-1 space-y-1">
                                            <li v-for="recommendation in recommendations" :key="recommendation">
                                                {{ recommendation }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Timeline -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Progress Timeline</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Goal Created</div>
                                        <div class="text-xs text-gray-500">{{ createdDate }}</div>
                                    </div>
                                </div>
                                <div v-if="progressPercentage > 0" class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">First Task Completed</div>
                                        <div class="text-xs text-gray-500">Progress started</div>
                                    </div>
                                </div>
                                <div v-if="progressPercentage >= 50" class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Halfway There!</div>
                                        <div class="text-xs text-gray-500">50% milestone reached</div>
                                    </div>
                                </div>
                                <div v-if="progressPercentage >= 100" class="flex items-center">
                                    <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Goal Completed!</div>
                                        <div class="text-xs text-gray-500">Congratulations!</div>
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
                            @click="shareGoal"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Share Progress
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
