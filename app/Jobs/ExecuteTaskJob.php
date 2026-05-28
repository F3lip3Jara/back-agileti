<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\LinearService;
use App\Services\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Task $task;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     */
    public function handle(LinearService $linear, OllamaService $ollama): void
    {
        $this->task->status = 'running';
        $this->task->addLog("Starting task execution. Agent: {$this->task->agent}", "INFO");

        try {
            $issueContext = "";
            $issueTitle = $this->task->title;

            // 1. Fetch Linear Context if available
            if ($this->task->linear_issue_id) {
                $this->task->addLog("Fetching issue details from Linear: {$this->task->linear_issue_id}", "INFO");
                $issue = $linear->getIssue($this->task->linear_issue_id);

                if ($issue) {
                    $issueTitle = $issue['title'];
                    $issueDescription = $issue['description'] ?? 'No description provided.';
                    $this->task->addLog("Successfully retrieved Linear Issue. Title: \"$issueTitle\"", "INFO");
                    
                    $issueContext = "\n---\n";
                    $issueContext .= "CONTEXTO DE LINEAR:\n";
                    $issueContext .= "Issue: {$this->task->linear_issue_id}\n";
                    $issueContext .= "Título: {$issueTitle}\n";
                    $issueContext .= "Descripción:\n{$issueDescription}\n";
                    $issueContext .= "---\n";
                } else {
                    $this->task->addLog("Warning: Could not fetch details for Linear issue {$this->task->linear_issue_id}. Proceeding with local details.", "WARN");
                }
            }

            // 2. Compile dynamic Prompt
            $systemInstruction = "Eres un Arquitecto de Software Senior y Desarrollador Fullstack experto. Tu objetivo es generar código o pruebas unitarias de producción de alta calidad basadas en el requerimiento proporcionado.";
            
            $prompt = "REQUERIMIENTO PRINCIPAL:\n";
            $prompt .= "Título de Tarea: {$this->task->title}\n";
            $prompt .= "Descripción / Prompt original: {$this->task->prompt}\n";
            $prompt .= $issueContext;
            $prompt .= "\nINSTRUCCIONES DE SALIDA:\n";
            $prompt .= "1. Genera código limpio, estructurado y modular que resuelva el requerimiento.\n";
            $prompt .= "2. Si es Laravel o Angular, sigue los estándares de diseño del proyecto.\n";
            $prompt .= "3. Incluye comentarios explicativos necesarios.\n";
            $prompt .= "4. Retorna el código fuente completo o las pruebas unitarias directamente.";

            $this->task->prompt = $prompt;
            $this->task->save();

            // 3. Execute AI Generation
            if ($this->task->agent === 'ollama') {
                $this->task->addLog("Dispatched request to Ollama API for code generation...", "INFO");
                
                $response = $ollama->generate($prompt, $systemInstruction);
                
                $this->task->output = $response;
                $this->task->status = 'completed';
                $this->task->addLog("Ollama successfully generated output. Length: " . strlen($response) . " bytes.", "INFO");
                $this->task->addLog("Task completed successfully.", "INFO");
                $this->task->save();

                // 4. Update Linear (Transition status and post comment)
                if ($this->task->linear_issue_id) {
                    $this->task->addLog("Updating Linear issue status to 'Done'...", "INFO");
                    
                    // Post completion comment
                    $commentText = "🤖 **[Orquestador AI]** Tarea completada con éxito.\n\n";
                    $commentText .= "El agente **Ollama (" . env('OLLAMA_MODEL', 'qwen2.5-coder') . ")** ha generado el código requerido:\n\n";
                    $commentText .= "```markdown\n" . substr($response, 0, 1000) . (strlen($response) > 1000 ? "\n... (truncado para visualización) ..." : "") . "\n```\n\n";
                    $commentText .= "Logs de la tarea:\n" . $this->task->logs;
                    
                    $issue = $linear->getIssue($this->task->linear_issue_id);
                    if ($issue) {
                        $linear->addComment($issue['id'], $commentText);
                        $linear->transitionToState($this->task->linear_issue_id, 'Done');
                        $this->task->addLog("Linear issue {$this->task->linear_issue_id} successfully commented and moved to 'Done'.", "INFO");
                    }
                }
            } else {
                throw new \Exception("Agent type '{$this->task->agent}' is not supported yet.");
            }

        } catch (\Exception $e) {
            $this->task->status = 'failed';
            $this->task->addLog("Execution failed with exception: " . $e->getMessage(), "ERROR");
            $this->task->addLog("Stack trace: " . substr($e->getTraceAsString(), 0, 500), "ERROR");
            $this->task->save();

            // Post failure comment to Linear
            if ($this->task->linear_issue_id) {
                try {
                    $issue = $linear->getIssue($this->task->linear_issue_id);
                    if ($issue) {
                        $commentText = "❌ 🤖 **[Orquestador AI]** La tarea ha FALLADO.\n\n";
                        $commentText .= "**Error:** {$e->getMessage()}\n\n";
                        $commentText .= "Revisa la consola de administración en el Dashboard de Angular.";
                        $linear->addComment($issue['id'], $commentText);
                        $linear->transitionToState($this->task->linear_issue_id, 'Backlog'); // Move back to backlog or triage
                    }
                } catch (\Exception $le) {
                    Log::error("Failed to post error feedback comment to Linear: " . $le->getMessage());
                }
            }
        }
    }
}
