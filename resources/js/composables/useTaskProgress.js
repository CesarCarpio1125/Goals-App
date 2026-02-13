import { computed } from 'vue';

export function useTaskProgress(tasks) {
    const progressPercentage = computed(() => {
        const totalTasks = tasks.value?.length || 0;
        if (totalTasks === 0) return 0;
        
        const completedTasks = tasks.value?.filter(t => t.completed).length || 0;
        return Math.round((completedTasks / totalTasks) * 100);
    });

    const taskProgress = computed(() => {
        const totalTasks = tasks.value?.length || 0;
        const completedTasks = tasks.value?.filter(t => t.completed).length || 0;
        const pendingTasks = totalTasks - completedTasks;
        
        return {
            total: totalTasks,
            completed: completedTasks,
            pending: pendingTasks,
            percentage: totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0
        };
    });

    const completedTasks = computed(() => tasks.value?.filter(t => t.completed).length || 0);
    const totalTasks = computed(() => tasks.value?.length || 0);
    const pendingTasks = computed(() => totalTasks.value - completedTasks.value);

    return {
        progressPercentage,
        taskProgress,
        completedTasks,
        totalTasks,
        pendingTasks
    };
}
