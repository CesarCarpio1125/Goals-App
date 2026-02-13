<?php

namespace App\Http\Controllers;

use App\Services\CalendarExportService;
use App\Services\DashboardService;
use App\Services\GoalService;
use App\Services\MotivationAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function __construct(
        private GoalService $goalService,
        private DashboardService $dashboardService,
        private CalendarExportService $calendarExportService
    ) {}

    public function index(Request $request): InertiaResponse
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

    public function exportPdf(Request $request): Response
    {
        $user = $request->user();

        // Get ALL goals with tasks for complete analytics (not just 6 like dashboard view)
        $goals = $this->goalService->getUserGoals($user)
            ->map(function ($goal) {
                $goal->load('tasks');
                return $goal;
            });

        // Calculate stats using all goals
        $stats = $this->goalService->getGoalsStats($user);

        // Get motivational message
        $motivationalMessage = $this->dashboardService->getMotivationalMessage($user);

        // Calculate streak
        $streak = $this->dashboardService->getUserStreak($user);

        $pdf = $this->calendarExportService->generateDashboardPdf(
            $user,
            $goals,
            $stats,
            $motivationalMessage,
            $streak
        );

        $filename = 'dashboard-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
