<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private GoalService $goalService,
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get goals with tasks
        $goals = $this->goalService->getUserGoals($user)
            ->take(6)
            ->map(function ($goal) {
                $goal->load('tasks');
                return $goal->toArray();
            });

        // Calculate stats using the same method as goals page
        $stats = $this->goalService->getGoalsStats($user);
        
        // Get motivational message based on progress
        $motivationalMessage = $this->dashboardService->getMotivationalMessage($user);
        
        // Calculate streak
        $streak = $this->dashboardService->getUserStreak($user);
        
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'goals' => $goals,
            'motivationalMessage' => $motivationalMessage,
            'streak' => $streak,
        ]);
    }
}
