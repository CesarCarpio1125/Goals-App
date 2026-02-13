<?php

namespace App\Services;

use App\Models\User;
use App\Services\MotivationAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class CalendarExportService
{
    public function __construct(
        private MotivationAnalysisService $motivationService
    ) {}
    public function generateCalendarPdf(User $user, Collection $goals, ?string $month = null, ?int $year = null): \Barryvdh\DomPDF\PDF
    {
        // Prepare data for the PDF
        $calendarData = $this->prepareCalendarData($goals, $month, $year);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.calendar', [
            'user' => $user,
            'goals' => $goals,
            'calendarData' => $calendarData,
            'month' => $month,
            'year' => $year,
        ]);

        // Configure PDF settings
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    private function prepareCalendarData(Collection $goals, ?string $month, ?int $year): array
    {
        // If no month/year specified, use current
        $currentDate = $month && $year ? new \DateTime("$year-$month-01") : new \DateTime();

        $monthName = $currentDate->format('F');
        $year = $currentDate->format('Y');

        // Group goals by their deadlines
        $goalsByDate = $goals->groupBy(function ($goal) {
            return $goal->deadline ? $goal->deadline->format('Y-m-d') : null;
        });

        // Get tasks with deadlines
        $tasksWithDeadlines = collect();
        $goals->each(function ($goal) use (&$tasksWithDeadlines) {
            if ($goal->tasks) {
                $goal->tasks->each(function ($task) use (&$tasksWithDeadlines, $goal) {
                    if ($task->deadline) {
                        $task->goal_title = $goal->title;
                        $tasksWithDeadlines->push($task);
                    }
                });
            }
        });

        $tasksByDate = $tasksWithDeadlines->groupBy(function ($task) {
            return $task->deadline->format('Y-m-d');
        });

        return [
            'month' => $monthName,
            'year' => $year,
            'goalsByDate' => $goalsByDate,
            'tasksByDate' => $tasksByDate,
            'totalGoals' => $goals->count(),
            'totalTasks' => $tasksWithDeadlines->count(),
        ];
    }

    public function generateDashboardPdf(User $user, Collection $goals, array $stats, ?string $motivationalMessage = null, ?int $streak = 0): \Barryvdh\DomPDF\PDF
    {
        // Prepare data for the PDF
        $dashboardData = $this->prepareDashboardData($goals, $stats, $motivationalMessage, $streak);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.dashboard', [
            'user' => $user,
            'goals' => $goals,
            'stats' => $stats,
            'dashboardData' => $dashboardData,
            'motivationalMessage' => $motivationalMessage,
            'streak' => $streak,
        ]);

        // Configure PDF settings
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    private function prepareDashboardData(Collection $goals, array $stats, ?string $motivationalMessage, ?int $streak): array
    {
        // Calculate completion rates for each goal using model's built-in methods
        $goalsWithProgress = $goals->map(function ($goal) {
            $taskProgress = $goal->task_progress; // Use model's built-in method
            $totalTasks = $taskProgress['total'];
            $completedTasks = $taskProgress['completed'];
            $progress = $taskProgress['percentage'];

            // Calculate goal age and velocity - fix unrealistic calculations
            $createdAt = $goal->created_at;
            $daysSinceCreation = max(1, $createdAt ? $createdAt->diffInDays(now()) : 1); // Minimum 1 day to avoid division by zero

            // Velocity: progress percentage achieved per day (more realistic calculation)
            $completionVelocity = $daysSinceCreation > 0 ? round(($progress / $daysSinceCreation), 2) : 0;

            // Cap velocity at reasonable maximum (100% per day is already very fast)
            $completionVelocity = min($completionVelocity, 100.0);

            // Priority score based on deadline proximity and progress
            $priorityScore = $this->calculatePriorityScore($goal, $progress);

            // Get task status breakdown - tasks use 'completed' boolean field
            $completedCount = $goal->tasks()->where('completed', true)->count();
            $pendingCount = $goal->tasks()->where('completed', false)->count();
            $inProgressCount = 0; // Tasks don't have in-progress status, just completed/pending

            return [
                'id' => $goal->id,
                'title' => $goal->title,
                'description' => $goal->description,
                'status' => $progress >= 100 ? 'completed' : 'in_progress', // Status based on progress, not model property
                'priority' => $goal->priority ?? 'medium', // Safe access with default
                'progress' => $progress,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedCount,
                'in_progress_tasks' => $inProgressCount, // Always 0 since tasks don't have this status
                'pending_tasks' => $pendingCount,
                'created_at' => $goal->created_at,
                'deadline' => $goal->deadline,
                'days_since_creation' => $daysSinceCreation,
                'completion_velocity' => $completionVelocity, // % achieved per day (capped at 100)
                'priority_score' => $priorityScore,
                'estimated_completion_days' => $this->estimateCompletionDays($progress, $completionVelocity),
            ];
        });

        // Group goals by status
        $goalsByStatus = $goalsWithProgress->groupBy('status');

        // Calculate advanced analytics
        $analytics = $this->calculateAdvancedAnalytics($goalsWithProgress, $stats);

        // Generate motivation analysis
        $motivationAnalysis = $this->motivationService->analyzePerformance($analytics, $stats, $streak);

        return [
            'goals_with_progress' => $goalsWithProgress,
            'goals_by_status' => $goalsByStatus,
            'completion_rate' => $stats['completion_rate'] ?? 0,
            'streak' => $streak,
            'motivational_message' => $motivationalMessage,
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'analytics' => $analytics,
            'motivation_analysis' => $motivationAnalysis,
        ];
    }

    private function calculatePriorityScore($goal, $progress): int
    {
        $score = 0;

        // Base priority mapping
        $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
        $score += $priorityMap[$goal->priority] ?? 1;

        // Deadline proximity (higher score if deadline is near)
        if ($goal->deadline) {
            $daysUntilDeadline = now()->diffInDays($goal->deadline, false);
            if ($daysUntilDeadline <= 7 && $daysUntilDeadline > 0) {
                $score += 3; // Urgent
            } elseif ($daysUntilDeadline <= 30 && $daysUntilDeadline > 0) {
                $score += 2; // Soon
            } elseif ($daysUntilDeadline < 0) {
                $score += 4; // Overdue
            }
        }

        // Progress-based adjustment (lower progress = higher priority)
        if ($progress < 25) {
            $score += 2;
        } elseif ($progress < 50) {
            $score += 1;
        }

        return min($score, 10); // Cap at 10
    }

    private function estimateCompletionDays($currentProgress, $completionVelocity): ?int
    {
        if ($completionVelocity <= 0 || $currentProgress >= 100) {
            return null;
        }

        $remainingProgress = 100 - $currentProgress;
        return ceil($remainingProgress / $completionVelocity);
    }

    private function calculateAdvancedAnalytics(Collection $goalsWithProgress, array $stats): array
    {
        $totalGoals = $goalsWithProgress->count();
        $completedGoals = $goalsWithProgress->where('status', 'completed')->count();
        $inProgressGoals = $goalsWithProgress->where('status', 'in_progress')->count();
        $pendingGoals = $totalGoals - $completedGoals - $inProgressGoals; // Goals that aren't completed or in progress

        // Productivity metrics
        $avgCompletionVelocity = $goalsWithProgress->where('completion_velocity', '>', 0)->avg('completion_velocity') ?? 0;
        $avgCompletionVelocity = round($avgCompletionVelocity, 2);
        $highPriorityGoals = $goalsWithProgress->where('priority_score', '>=', 7)->count();
        $overdueGoals = $goalsWithProgress->filter(function ($goal) {
            return $goal['deadline'] && $goal['deadline']->isPast() && $goal['status'] !== 'completed';
        })->count();

        // Progress distribution - use actual progress values
        $progressRanges = [
            '0-25%' => $goalsWithProgress->where('progress', '<=', 25)->count(),
            '26-50%' => $goalsWithProgress->where('progress', '>', 25)->where('progress', '<=', 50)->count(),
            '51-75%' => $goalsWithProgress->where('progress', '>', 50)->where('progress', '<=', 75)->count(),
            '76-99%' => $goalsWithProgress->where('progress', '>', 75)->where('progress', '<', 100)->count(),
            '100%' => $goalsWithProgress->where('progress', '=', 100)->count(),
        ];

        // Time-based analysis
        $goalsCreatedThisWeek = $goalsWithProgress->filter(function ($goal) {
            return $goal['created_at'] && $goal['created_at']->isCurrentWeek();
        })->count();

        $goalsCompletedThisWeek = $goalsWithProgress->filter(function ($goal) {
            return $goal['status'] === 'completed' &&
                   $goal['created_at'] &&
                   $goal['created_at']->isCurrentWeek();
        })->count();

        // Task completion patterns - use the actual task counts from goals
        $totalTasks = $goalsWithProgress->sum('total_tasks');
        $completedTasks = $goalsWithProgress->sum('completed_tasks');
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        return [
            'total_goals' => $totalGoals,
            'completed_goals' => $completedGoals,
            'in_progress_goals' => $inProgressGoals,
            'pending_goals' => $pendingGoals,
            'high_priority_goals' => $highPriorityGoals,
            'overdue_goals' => $overdueGoals,
            'avg_completion_velocity' => round($avgCompletionVelocity, 2),
            'progress_distribution' => $progressRanges,
            'goals_created_this_week' => $goalsCreatedThisWeek,
            'goals_completed_this_week' => $goalsCompletedThisWeek,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'task_completion_rate' => $taskCompletionRate,
            'productivity_score' => $this->calculateProductivityScore($stats, $avgCompletionVelocity, $overdueGoals),
        ];
    }

    private function calculateProductivityScore(array $stats, float $avgVelocity, int $overdueGoals): int
    {
        $score = 50; // Base score

        // Completion rate bonus
        $completionRate = $stats['completion_rate'] ?? 0;
        $score += ($completionRate - 50) * 0.5; // Bonus/penalty for completion rate

        // Velocity bonus
        if ($avgVelocity > 2) $score += 20;
        elseif ($avgVelocity > 1) $score += 10;
        elseif ($avgVelocity < 0.5) $score -= 15;

        // Overdue penalty
        $score -= $overdueGoals * 5;

        return max(0, min(100, round($score)));
    }
}
