<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Http\Requests\UpdateGoalProgressRequest;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        $goals = $this->goalService->getUserGoals(
            $request->user(),
            $request->only(['status', 'deadline_from', 'deadline_to'])
        );

        $stats = $this->goalService->getGoalsStats($request->user());

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

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal created successfully!');
    }

    public function show($hash)
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goal = Goal::byHash($hash)->first();
        
        if (!$goal) {
            // Fallback temporal mientras se migra la BD
            $goal = Goal::find($hash);
            // No intentar guardar hash si la columna no existe
        }
        
        if (!$goal) {
            abort(404);
        }
        
        $this->authorize('view', $goal);

        $goal->load('tasks');

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
        ]);
    }

    public function update(UpdateGoalRequest $request, $hash): RedirectResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goal = Goal::byHash($hash)->first();
        
        if (!$goal) {
            $goal = Goal::find($hash);
            // No intentar guardar hash si la columna no existe
        }
        
        if (!$goal) {
            abort(404);
        }
        
        $this->authorize('update', $goal);

        $updatedGoal = $this->goalService->updateGoal(
            $goal,
            $request->validated()
        );

        return redirect()
            ->route('goals.show', $goal->hash ?? $goal->id)
            ->with('success', 'Goal updated successfully!');
    }

    public function edit($hash)
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goal = Goal::byHash($hash)->first();
        
        if (!$goal) {
            $goal = Goal::find($hash);
            // No intentar guardar hash si la columna no existe
        }
        
        if (!$goal) {
            abort(404);
        }
        
        $this->authorize('update', $goal);

        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
        ]);
    }

    public function updateProgress(UpdateGoalProgressRequest $request, $hash): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goal = Goal::byHash($hash)->first();
        
        if (!$goal) {
            $goal = Goal::find($hash);
            // No intentar guardar hash si la columna no existe
        }
        
        if (!$goal) {
            abort(404);
        }
        
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return response()->json([
            'goal' => $goal,
            'progress_percentage' => $goal->progress_percentage,
        ]);
    }

    public function destroy($hash): RedirectResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goal = Goal::byHash($hash)->first();
        
        if (!$goal) {
            $goal = Goal::find($hash);
            // No intentar guardar hash si la columna no existe
        }
        
        if (!$goal) {
            abort(404);
        }
        
        $this->authorize('delete', $goal);

        $this->goalService->deleteGoal($goal);

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal deleted successfully!');
    }
}
