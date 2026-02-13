<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    goal: {
        type: Object,
        required: true
    }
});

const form = useForm({
    title: props.goal.title,
    description: props.goal.description,
    deadline: props.goal.deadline ? new Date(props.goal.deadline).toISOString().split('T')[0] : '',
});

const submit = () => {
    form.patch(route('goals.update', props.goal.hash || props.goal.id), {
        onSuccess: (page) => {
            // Handle success
        },
        onError: (errors) => {
            console.error('Update errors:', errors);
        },
    });
};

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
</script>

<template>
    <Head :title="`Edit ${goal.title}`" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4 mb-4">
                    <Link :href="route('goals.show', goal.hash || goal.id)" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Goal
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900">Edit Goal</h1>
                <p class="mt-2 text-gray-600">Update your goal information</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Goal Information</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Update the details of your goal</p>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">
                                        Goal Title
                                    </label>
                                    <div class="mt-1">
                                        <input
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            required
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Enter your goal title"
                                        />
                                        <div v-if="form.errors.title" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.title }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <div class="mt-1">
                                        <textarea
                                            id="description"
                                            v-model="form.description"
                                            rows="4"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Describe your goal in detail"
                                        ></textarea>
                                        <div v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.description }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Target Value -->
                                <div>
                                    <label for="target_value" class="block text-sm font-medium text-gray-700">
                                        Target Value
                                    </label>
                                    <div class="mt-1">
                                        <input
                                            id="target_value"
                                            v-model.number="form.target_value"
                                            type="number"
                                            required
                                            min="1"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="100"
                                        />
                                        <div v-if="form.errors.target_value" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.target_value }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Deadline -->
                                <div>
                                    <label for="deadline" class="block text-sm font-medium text-gray-700">
                                        Deadline (Optional)
                                    </label>
                                    <div class="mt-1">
                                        <input
                                            id="deadline"
                                            v-model="form.deadline"
                                            type="date"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <div v-if="form.errors.deadline" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.deadline }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end space-x-3">
                                    <Link
                                        :href="route('goals.show', goal.hash || goal.id)"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Current Goal Info -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Current Goal</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-6 space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Title</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ goal.title }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ goal.description || 'No description' }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500">Progress</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ goal.target_value > 0 ? Math.round((goal.current_value / goal.target_value) * 100) : 0 }}%
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formattedDeadline }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Editing Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Set realistic and measurable targets</li>
                                        <li>Break large goals into smaller milestones</li>
                                        <li>Set deadlines to create accountability</li>
                                        <li>Update your progress regularly</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
