<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    title: '',
    description: '',
    deadline: '',
});

const submit = async () => {
    // Prevent empty submissions
    if (!form.title.trim()) {
        return;
    }
    
    try {
        await form.post(route('goals.store'), {
            onSuccess: () => {
                form.reset();
            },
            onError: (errors) => {
                console.error('Error creating goal:', errors);
            },
        });
    } catch (err) {
        console.error('Error details:', err.message);
    }
};
</script>

<template>
    <Head title="Create Goal" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-12">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Create New Goal
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        Set a new goal to track your progress.
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Goal Title
                            </label>
                            <div class="mt-1">
                                <input
                                    type="text"
                                    id="title"
                                    v-model="form.title"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                    placeholder="Enter your goal title..."
                                />
                            </div>
                            <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                />
                            </div>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">
                                Deadline (Fecha de Vencimiento) - Opcional
                            </label>
                            <div class="mt-1">
                                <input
                                    type="date"
                                    id="deadline"
                                    v-model="form.deadline"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :min="new Date().toISOString().split('T')[0]"
                                />
                            </div>
                            <p v-if="form.errors.deadline" class="mt-2 text-sm text-red-600">
                                {{ form.errors.deadline }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Establece una fecha límite para tu objetivo (opcional)
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Goal</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
