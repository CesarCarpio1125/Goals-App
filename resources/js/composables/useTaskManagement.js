import { ref } from 'vue';

export function useTaskManagement(goalId, initialTasks = []) {
    const tasks = ref(initialTasks);
    const newTaskTitle = ref('');
    const isAddingTask = ref(false);
    const isLoading = ref(false);

    // Motivational feedback
    const motivationalMessage = ref('');
    const recommendations = ref([]);
    const showFeedback = ref(false);

    const addTask = async () => {
        if (!newTaskTitle.value.trim()) return;
        
        isLoading.value = true;
        
        try {
            const response = await fetch(route('goals.tasks.store', goalId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: newTaskTitle.value,
                    description: '',
                    order: tasks.value.length
                })
            });
            
            if (response.ok) {
                const newTask = await response.json();
                tasks.value.push(newTask);
                newTaskTitle.value = '';
                isAddingTask.value = false;
            }
        } catch (error) {
            console.error('Error adding task:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const toggleTask = async (task) => {
        console.log('toggleTask called with:', { task, goalId, taskId: task?.id });
        
        if (!task || !task.id) {
            console.error('Task or task.id is missing:', task);
            return;
        }
        
        if (!goalId) {
            console.error('Goal ID is missing:', goalId);
            return;
        }
        
        try {
            const routeUrl = route('goals.tasks.toggle', { goal: goalId, task: task.id });
            console.log('Generated route URL:', routeUrl);
            
            const response = await fetch(routeUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Find and update the task to trigger reactivity
                const index = tasks.value.findIndex(t => t.id === task.id);
                if (index !== -1) {
                    // Create a new array to trigger reactivity
                    const updatedTasks = [...tasks.value];
                    updatedTasks[index] = { ...data.task };
                    tasks.value = updatedTasks;
                }
                
                // Update motivational feedback
                motivationalMessage.value = data.goal.motivational_message;
                recommendations.value = data.goal.recommendations;
                showFeedback.value = true;
                
                // Set flash message for list page refresh
                if (typeof window !== 'undefined' && window.sessionStorage) {
                    sessionStorage.setItem('goal_updated', 'true');
                }
                
                // Hide feedback after 5 seconds
                setTimeout(() => {
                    showFeedback.value = false;
                }, 5000);
            }
        } catch (error) {
            console.error('Error toggling task:', error);
        }
    };

    const deleteTask = async (task) => {
        if (!confirm('Are you sure you want to delete this task?')) return;
        
        try {
            const response = await fetch(route('goals.tasks.destroy', { goal: goalId, task: task.id }), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                tasks.value = tasks.value.filter(t => t.id !== task.id);
            }
        } catch (error) {
            console.error('Error deleting task:', error);
        }
    };

    return {
        tasks,
        newTaskTitle,
        isAddingTask,
        isLoading,
        motivationalMessage,
        recommendations,
        showFeedback,
        addTask,
        toggleTask,
        deleteTask
    };
}
