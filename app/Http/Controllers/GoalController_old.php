<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalProgressRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class GoalController extends Controller
{
    public function __construct(
        private GoalService $goalService
    ) {}

    public function index(Request $request)
    {
        \Log::info('Goals index called for user: ' . $request->user()->id);
        
        $goals = $this->goalService->getUserGoals(
            $request->user(),
            $request->only(['status', 'deadline_from', 'deadline_to'])
        );

        $stats = $this->goalService->getGoalsStats($request->user());
        
        \Log::info('Goals count: ' . $goals->count());
        \Log::info('Stats: ', (array) $stats);

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
            'stats' => $stats,
            'filters' => $request->only(['status', 'deadline_from', 'deadline_to']),
        ]);
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $goal = $this->goalService->createGoal(
            $request->user(),
            $request->validated()
        );

        return redirect()->route('dashboard')->with('success', 'Goal created successfully!');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);

        $progress = $this->goalService->getGoalProgress($goal);

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
            'progress' => $progress,
        ]);
    }

    public function update(UpdateGoalRequest $request, Goal $goal): JsonResponse
    {
        $this->authorize('update', $goal);

        $updatedGoal = $this->goalService->updateGoal(
            $goal,
            $request->validated()
        );

        return response()->json([
            'message' => 'Goal updated successfully',
            'goal' => $updatedGoal,
        ]);
    }

    public function updateProgress(
        UpdateGoalProgressRequest $request,
        Goal $goal
    ): JsonResponse {
        $this->authorize('update', $goal);

        $updatedGoal = $this->goalService->updateProgress(
            $goal,
            $request->validated()['current_value']
        );

        $progress = $this->goalService->getGoalProgress($updatedGoal);

        return response()->json([
            'message' => 'Goal progress updated successfully',
            'goal' => $updatedGoal,
            'progress' => $progress,
        ]);
    }

    public function destroy(Goal $goal): JsonResponse
    {
        $this->authorize('delete', $goal);

        $this->goalService->deleteGoal($goal);

        return response()->json([
            'message' => 'Goal deleted successfully',
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->goalService->getGoalsStats($request->user());

        return response()->json($stats);
    }

    public function overdue(Request $request): JsonResponse
    {
        $overdueGoals = $this->goalService->getOverdueGoals($request->user());

        return response()->json([
            'overdue_goals' => $overdueGoals,
            'count' => $overdueGoals->count(),
        ]);
    }
}
