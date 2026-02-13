import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useGoalStore = defineStore('goal', {
    state: () => ({
        goals: [],
        stats: {
            total: 0,
            completed: 0,
            in_progress: 0,
            completion_rate: 0,
        },
        loading: false,
        error: null,
        filters: {
            status: null,
            deadline_from: null,
            deadline_to: null,
        },
    }),

    getters: {
        activeGoals: (state) => {
            return state.goals.filter(goal => goal.current_value < goal.target_value)
        },
        
        completedGoals: (state) => {
            return state.goals.filter(goal => goal.current_value >= goal.target_value)
        },
        
        overdueGoals: (state) => {
            return state.goals.filter(goal => {
                const isOverdue = goal.deadline && new Date(goal.deadline) < new Date()
                const isNotCompleted = goal.current_value < goal.target_value
                return isOverdue && isNotCompleted
            })
        },
        
        goalsByProgress: (state) => {
            const progressRanges = {
                justStarted: [],
                makingProgress: [],
                almostThere: [],
                completed: [],
            }
            
            state.goals.forEach(goal => {
                const percentage = Math.round((goal.current_value / goal.target_value) * 100)
                
                if (percentage >= 100) {
                    progressRanges.completed.push(goal)
                } else if (percentage >= 75) {
                    progressRanges.almostThere.push(goal)
                } else if (percentage >= 25) {
                    progressRanges.makingProgress.push(goal)
                } else {
                    progressRanges.justStarted.push(goal)
                }
            })
            
            return progressRanges
        },
        
        motivationalMessage: (state) => {
            const completionRate = state.stats.completion_rate
            
            if (completionRate >= 100) {
                return "Goal completed. You're unstoppable."
            } else if (completionRate >= 75) {
                return "You're almost there. Finish strong."
            } else if (completionRate >= 50) {
                return "You're making serious progress. Stay focused."
            } else if (completionRate >= 25) {
                return "You're building momentum. Keep pushing."
            } else if (state.goals.length === 0) {
                return "Every great journey begins with a single step."
            } else {
                return "You're just getting started. Keep pushing."
            }
        },
        
        streak: (state) => {
            // This would typically come from the backend
            // For now, we'll calculate based on recent activity
            return Math.floor(Math.random() * 30) + 1
        },
    },

    actions: {
        async fetchGoals(filters = {}) {
            this.loading = true
            this.error = null
            
            try {
                const response = await router.get('/goals', filters, {
                    preserveState: false,
                    preserveScroll: true,
                })
                
                this.goals = response.props.goals.data || []
                this.stats = response.props.stats || this.stats
                this.filters = { ...this.filters, ...filters }
            } catch (error) {
                this.error = 'Failed to fetch goals'
                console.error('Error fetching goals:', error)
            } finally {
                this.loading = false
            }
        },
        
        async createGoal(goalData) {
            this.loading = true
            this.error = null
            
            try {
                const response = await router.post('/goals', goalData, {
                    preserveState: false,
                    onSuccess: () => {
                        this.fetchGoals(this.filters)
                    }
                })
                
                return response
            } catch (error) {
                this.error = 'Failed to create goal'
                console.error('Error creating goal:', error)
                throw error
            } finally {
                this.loading = false
            }
        },
        
        async updateGoal(goalId, goalData) {
            this.loading = true
            this.error = null
            
            try {
                const response = await router.put(`/goals/${goalId}`, goalData, {
                    preserveState: false,
                    onSuccess: () => {
                        this.fetchGoals(this.filters)
                    }
                })
                
                return response
            } catch (error) {
                this.error = 'Failed to update goal'
                console.error('Error updating goal:', error)
                throw error
            } finally {
                this.loading = false
            }
        },
        
        async updateProgress(goalId, currentValue) {
            this.loading = true
            this.error = null
            
            try {
                const response = await router.patch(`/goals/${goalId}/progress`, {
                    current_value: currentValue
                }, {
                    preserveState: false,
                    onSuccess: () => {
                        this.fetchGoals(this.filters)
                    }
                })
                
                return response
            } catch (error) {
                this.error = 'Failed to update progress'
                console.error('Error updating progress:', error)
                throw error
            } finally {
                this.loading = false
            }
        },
        
        async deleteGoal(goalId) {
            this.loading = true
            this.error = null
            
            try {
                const response = await router.delete(`/goals/${goalId}`, {
                    preserveState: false,
                    onSuccess: () => {
                        this.fetchGoals(this.filters)
                    }
                })
                
                return response
            } catch (error) {
                this.error = 'Failed to delete goal'
                console.error('Error deleting goal:', error)
                throw error
            } finally {
                this.loading = false
            }
        },
        
        setFilters(filters) {
            this.filters = { ...this.filters, ...filters }
            this.fetchGoals(this.filters)
        },
        
        clearError() {
            this.error = null
        },
    },
})
