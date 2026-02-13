export function calculateTaskProgress(goal) {
    if (!goal || !goal.tasks) {
        return {
            totalTasks: 0,
            completedTasks: 0,
            pendingTasks: 0,
            progressPercentage: 0,
            status: 'pending'
        };
    }
    
    const totalTasks = goal.tasks.length;
    const completedTasks = goal.tasks.filter(task => task.completed).length;
    const pendingTasks = totalTasks - completedTasks;
    const progressPercentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
    
    let status;
    if (totalTasks === 0) {
        status = 'pending';
    } else if (completedTasks === totalTasks) {
        status = 'completed';
    } else if (completedTasks > 0) {
        status = 'in_progress';
    } else {
        status = 'pending';
    }
    
    return {
        totalTasks,
        completedTasks,
        pendingTasks,
        progressPercentage,
        status
    };
}
