<?php

namespace App\Services;

use App\DTOs\GoalDTO;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Collection;

class GoalService
{
    public function getUserGoals(User $user, array $filters = []): Collection
    {
        $query = $user->goals()->with('tasks');

        // Apply filters with better null checking
        if (!empty($filters['deadline_from'])) {
            $query->where('deadline', '>=', $filters['deadline_from']);
        }

        if (!empty($filters['deadline_to'])) {
            $query->where('deadline', '<=', $filters['deadline_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function createGoal(User $user, array $data): Goal
    {
        $goalDTO = GoalDTO::fromArray($data);
        
        return $user->goals()->create($goalDTO->toArray());
    }

    public function updateGoal(Goal $goal, array $data): Goal
    {
        $goalDTO = GoalDTO::fromArray($data);
        
        $goal->update($goalDTO->toArray());
        
        return $goal->fresh();
    }

    public function deleteGoal(Goal $goal): bool
    {
        return $goal->delete();
    }

    public function getGoalsStats(User $user): array
    {
        $goals = $user->goals()->with('tasks')->get();
        
        [$completed, $inProgress, $pending] = [0, 0, 0];
        
        foreach ($goals as $goal) {
            $totalTasks = $goal->tasks->count();
            $completedTasks = $goal->tasks->where('completed', true)->count();
            
            match (true) {
                $totalTasks === 0 => $pending++,
                $completedTasks === $totalTasks => $completed++,
                $completedTasks > 0 => $inProgress++,
                default => $pending++
            };
        }
        
        return [
            'total' => $goals->count(),
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $pending,
            'completion_rate' => $goals->count() > 0 
                ? round(($completed / $goals->count()) * 100, 2)
                : 0,
        ];
    }

    public function getGoalProgress(Goal $goal): array
    {
        $progress = $goal->target_value > 0 
            ? round(($goal->current_value / $goal->target_value) * 100, 2)
            : 0;

        return [
            'percentage' => $progress,
            'current_value' => $goal->current_value,
            'target_value' => $goal->target_value,
            'remaining' => max(0, $goal->target_value - $goal->current_value),
            'status' => $progress >= 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'pending'),
        ];
    }
}
