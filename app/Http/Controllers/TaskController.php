<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Goal;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index($goal): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goalModel = Goal::byHash($goal)->first();
        
        if (!$goalModel) {
            $goalModel = Goal::find($goal);
        }
        
        if (!$goalModel) {
            return response()->json(['error' => 'Goal not found'], 404);
        }
        
        $this->authorize('view', $goalModel);
        
        $tasks = $goalModel->tasks()->ordered()->get();
        
        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request, $goal): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goalModel = Goal::byHash($goal)->first();
        
        if (!$goalModel) {
            $goalModel = Goal::find($goal);
        }
        
        if (!$goalModel) {
            return response()->json(['error' => 'Goal not found'], 404);
        }
        
        $this->authorize('update', $goalModel);
        
        $task = $goalModel->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'completed' => false,
        ]);

        // Update goal progress automatically
        $goalModel->updateProgress();

        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, $goal, Task $task): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goalModel = Goal::byHash($goal)->first();
        
        if (!$goalModel) {
            $goalModel = Goal::find($goal);
        }
        
        if (!$goalModel) {
            return response()->json(['error' => 'Goal not found'], 404);
        }
        
        $this->authorize('update', $goalModel);
        
        if ($task->goal_id !== $goalModel->id) {
            return response()->json(['error' => 'Task does not belong to this goal'], 403);
        }

        $task->update($request->validated());

        // Update goal progress automatically
        $goalModel->updateProgress();

        return response()->json($task);
    }

    public function destroy($goal, Task $task): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goalModel = Goal::byHash($goal)->first();
        
        if (!$goalModel) {
            $goalModel = Goal::find($goal);
        }
        
        if (!$goalModel) {
            return response()->json(['error' => 'Goal not found'], 404);
        }
        
        $this->authorize('update', $goalModel);
        
        if ($task->goal_id !== $goalModel->id) {
            return response()->json(['error' => 'Task does not belong to this goal'], 403);
        }

        $task->delete();

        // Update goal progress automatically
        $goalModel->updateProgress();

        return response()->json(null, 204);
    }

    public function toggle($goal, Task $task): JsonResponse
    {
        // Intentar encontrar por hash, si no existe, intentar por ID (temporal)
        $goalModel = Goal::byHash($goal)->first();
        
        if (!$goalModel) {
            $goalModel = Goal::find($goal);
        }
        
        if (!$goalModel) {
            return response()->json(['error' => 'Goal not found'], 404);
        }
        
        $this->authorize('update', $goalModel);
        
        if ($task->goal_id !== $goalModel->id) {
            return response()->json(['error' => 'Task does not belong to this goal'], 403);
        }

        $task->toggleCompletion();

        // Update goal progress automatically
        $goalModel->updateProgress();

        // Return updated goal with motivational message
        return response()->json([
            'task' => $task,
            'goal' => [
                'progress_percentage' => $goalModel->progress_percentage,
                'task_progress' => $goalModel->task_progress,
                'motivational_message' => $goalModel->motivational_message,
                'recommendations' => $goalModel->recommendations
            ]
        ]);
    }
}
