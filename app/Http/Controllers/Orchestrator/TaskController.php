<?php

namespace App\Http\Controllers\Orchestrator;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Jobs\ExecuteTaskJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return response()->json($tasks);
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        return response()->json($task);
    }

    /**
     * Retry the specified task by re-queueing it.
     */
    public function retry(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        // Reset status, outputs, and clear/re-initialize logs
        $task->status = 'pending';
        $task->output = null;
        $task->logs = null;
        $task->addLog("Task manually queued for retry.", "INFO");
        $task->save();

        // Dispatch background execution
        ExecuteTaskJob::dispatch($task);

        return response()->json([
            'message' => 'Tarea re-encolada para ejecución exitosamente',
            'task' => $task
        ]);
    }

    /**
     * Cancel the specified task.
     */
    public function cancel(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        if (in_array($task->status, ['completed', 'failed', 'cancelled'])) {
            return response()->json([
                'error' => "La tarea ya finalizó con estado: {$task->status}. No se puede cancelar."
            ], 400);
        }

        $task->status = 'cancelled';
        $task->addLog("Task cancelled manually by the user.", "WARN");
        $task->save();

        return response()->json([
            'message' => 'Tarea cancelada exitosamente',
            'task' => $task
        ]);
    }
}
