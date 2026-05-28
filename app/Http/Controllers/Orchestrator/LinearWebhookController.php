<?php

namespace App\Http\Controllers\Orchestrator;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Jobs\ExecuteTaskJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LinearWebhookController extends Controller
{
    /**
     * Handle incoming Linear webhook payload.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        Log::info('Incoming Linear webhook payload:', [
            'action' => $payload['action'] ?? 'unknown',
            'type' => $payload['type'] ?? 'unknown',
            'identifier' => $payload['data']['identifier'] ?? 'unknown'
        ]);

        $action = $payload['action'] ?? null; // create, update, remove
        $type = $payload['type'] ?? null;     // Issue, Comment, Project, etc.

        if ($type !== 'Issue' || !in_array($action, ['create', 'update'])) {
            return response()->json([
                'success' => true,
                'message' => 'Ignored non-issue or non-modifiable event.'
            ]);
        }

        $issueData = $payload['data'] ?? [];
        $identifier = $issueData['identifier'] ?? null;
        $title = $issueData['title'] ?? 'Sin título';
        $description = $issueData['description'] ?? 'Generar código para resolver este issue.';
        
        // Check state
        $state = $issueData['state'] ?? [];
        $stateName = $state['name'] ?? '';
        $stateType = $state['type'] ?? ''; // started, unstarted, completed, etc.

        // Trigger task execution when issue state is set to "In Progress" or matches started type
        $isInProgress = (strcasecmp($stateName, 'In Progress') === 0) || (strcasecmp($stateType, 'started') === 0);

        if ($isInProgress) {
            // Check if there is already a running or pending task for this issue to prevent duplicate triggers
            $existingTask = Task::where('linear_issue_id', $identifier)
                ->whereIn('status', ['pending', 'running'])
                ->first();

            if ($existingTask) {
                Log::info("An orchestrator task for Linear issue $identifier is already active. Ignoring duplicate webhook.");
                return response()->json([
                    'success' => true,
                    'message' => 'Task is already active for this issue.'
                ]);
            }

            // Create new orchestrator task
            $task = Task::create([
                'linear_issue_id' => $identifier,
                'title' => "[Linear] " . $title,
                'status' => 'pending',
                'agent' => 'ollama',
                'prompt' => $description
            ]);

            $task->addLog("Task created automatically via Linear Webhook event '{$action}'.", "INFO");
            $task->addLog("Issue title: \"$title\". Trigger state: \"$stateName\".", "INFO");

            // Dispatch background execution
            ExecuteTaskJob::dispatch($task);

            return response()->json([
                'success' => true,
                'message' => 'Linear issue is In Progress. Task generated and dispatched successfully.',
                'task_id' => $task->id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Event ignored. Issue state is '$stateName' (type: '$stateType')."
        ]);
    }
}
