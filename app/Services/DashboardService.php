<?php

namespace App\Services;

use App\Models\User;
use App\Models\Goal;
use App\Services\GoalService;
use Carbon\Carbon;

class DashboardService
{
    public function getUserStats(User $user): array
    {
        // Delegate to GoalService for consistency
        return app(GoalService::class)->getGoalsStats($user);
    }
    
    public function getMotivationalMessage(User $user): string
    {
        $completionRate = $this->calculateCompletionRate($user);
        
        return match (true) {
            $completionRate >= 100 => "🎉 Incredible! You've crushed all your goals!",
            $completionRate >= 71 => "🔥 You're on fire! Almost there, keep pushing!",
            $completionRate >= 31 => "💪 Great progress! Consistency is key!",
            $completionRate > 0 => "🚀 Good start! Let's build momentum!",
            default => "🌟 Ready to start your journey? Create your first goal!",
        };
    }
    
    public function getUserStreak(User $user): int
    {
        // Calculate streak based on recent goal activity
        $recentGoals = $user->goals()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();
            
        if ($recentGoals->isEmpty()) {
            return 0;
        }
        
        // Simple streak calculation: consecutive days with goal activity
        $streak = 0;
        $currentDate = Carbon::now();
        
        foreach ($recentGoals as $goal) {
            $goalDate = $goal->created_at->startOfDay();
            $diffDays = $currentDate->startOfDay()->diffInDays($goalDate);
            
            if ($diffDays === $streak) {
                $streak++;
            } else {
                break;
            }
        }
        
        return $streak;
    }
    
    private function calculateCompletionRate(User $user): float
    {
        $goals = $user->goals()->with('tasks')->get();
        
        if ($goals->isEmpty()) {
            return 0;
        }
        
        $completedGoals = 0;
        foreach ($goals as $goal) {
            $goalArray = $goal->toArray();
            $tasks = $goalArray['tasks'] ?? [];
            
            $totalTasks = count($tasks);
            if ($totalTasks === 0) continue;
            
            $completedTasks = count(array_filter($tasks, fn($task) => $task['completed']));
            if ($completedTasks === $totalTasks) {
                $completedGoals++;
            }
        }
        
        return round(($completedGoals / $goals->count()) * 100, 2);
    }
}
