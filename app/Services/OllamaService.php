<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $apiUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('OLLAMA_API_URL', 'http://localhost:11434'), '/');
        $this->model = env('OLLAMA_MODEL', 'qwen2.5-coder');
        $this->timeout = (int) env('OLLAMA_TIMEOUT', 120); // Code generation might take time, set a high timeout default
    }

    /**
     * Call Ollama /api/generate endpoint to obtain a response.
     */
    public function generate(string $prompt, ?string $systemInstruction = null): string
    {
        try {
            $payload = [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
            ];

            if ($systemInstruction) {
                $payload['system'] = $systemInstruction;
            }

            Log::info("Calling Ollama API with model: {$this->model}");

            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/api/generate", $payload);

            if ($response->failed()) {
                Log::error('Ollama API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception("Ollama API failed with status {$response->status()}: " . $response->body());
            }

            $data = $response->json();
            if (!isset($data['response'])) {
                Log::error('Ollama API returned invalid JSON structure', ['response' => $data]);
                throw new \Exception("Ollama returned invalid response: " . $response->body());
            }

            return $data['response'];
        } catch (\Exception $e) {
            Log::error('Ollama service error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
