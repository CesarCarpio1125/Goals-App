import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

export function useGoals() {
    const goals = ref([])
    const loading = ref(false)
    const error = ref(null)

    const form = useForm({
        title: '',
        description: '',
        target_value: 100,
        current_value: 0,
        deadline: null,
    })

    // Computed properties
    const hasGoals = computed(() => goals.value.length > 0)
    const completedGoals = computed(() => goals.value.filter(goal => goal.status === 'completed'))
    const inProgressGoals = computed(() => goals.value.filter(goal => goal.status === 'in_progress'))
    const pendingGoals = computed(() => goals.value.filter(goal => goal.status === 'pending'))

    const completionRate = computed(() => {
        if (goals.value.length === 0) return 0
        return Math.round((completedGoals.value.length / goals.value.length) * 100)
    })

    // Methods
    const createGoal = async (goalData) => {
        loading.value = true
        error.value = null

        try {
            const response = await form.post(route('goals.store'), goalData)
            form.reset()

            // Return response for parent components to handle
            return response
        } catch (err) {
            error.value = 'Failed to create goal'
            console.error('Error creating goal:', err)
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateGoal = async (goalId, updates) => {
        loading.value = true
        error.value = null

        try {
            await form.patch(route('goals.update', goalId), updates)
        } catch (err) {
            error.value = 'Failed to update goal'
            console.error('Error updating goal:', err)
            throw err
        } finally {
            loading.value = false
        }
    }

    const deleteGoal = async (goalId) => {
            if (!confirm('Are you sure you want to delete this goal?')) return

            loading.value = true
            error.value = null

            try {
                await form.delete(route('goals.destroy', goalId))
            } catch (err) {
                error.value = 'Failed to delete goal'
                console.error('Error deleting goal:', err)
                throw err
            } finally {
                loading.value = false
            }
        }

    const refreshGoals = async () => {
        loading.value = true
        error.value = null

        try {
            await router.reload({ only: ['goals'] })
        } catch (err) {
            error.value = 'Failed to refresh goals'
            console.error('Error refreshing goals:', err)
            throw err
        } finally {
            loading.value = false
        }
    }

    const resetForm = () => {
        form.reset()
        error.value = null
    }

    const clearError = () => {
        error.value = null
    }

    return {
        // State
        goals,
        loading,
        error,
        form,

        // Computed
        hasGoals,
        completedGoals,
        inProgressGoals,
        pendingGoals,
        completionRate,

        // Methods
        createGoal,
        updateGoal,
        deleteGoal,
        refreshGoals,
        resetForm,
        clearError,
    }
}
