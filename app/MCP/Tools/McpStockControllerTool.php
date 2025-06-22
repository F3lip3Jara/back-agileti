<?php

namespace App\MCP\Tools;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use OPGG\LaravelMcpServer\Services\ToolService\ToolInterface;

class McpStockControllerTool implements ToolInterface
{
    /**
     * Get the tool name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'mcp-stock-controller';
    }

    /**
     * Get the tool description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Description of McpStockControllerTool';
    }

    /**
     * Get the input schema for the tool.
     *
     * @return array
     */
    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'param1' => [
                    'type' => 'string',
                    'description' => 'First parameter description',
                ],
                // Add more parameters as needed
            ],
            'required' => ['param1'],
        ];
    }

    /**
     * Get the tool annotations.
     *
     * @return array
     */
    public function getAnnotations(): array
    {
        return [];
    }

    /**
     * Execute the tool.
     *
     * @param array $arguments Tool arguments
     * @return mixed
     */
    public function execute(array $arguments): string
    {
        Validator::make($arguments, [
            'param1' => ['required', 'string'],
            // Add more validation rules as needed
        ])->validate();

        $param1 = $arguments['param1'] ?? 'default';

    //Voy a conectar ia 

      // Paso 3: Enviar a OpenRouter (IA gratuita con key)
      $respuesta = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        'Content-Type' => 'application/json'
    ])->withOptions([
        'verify' => false // Deshabilitar verificación SSL temporalmente
    ])->post('https://openrouter.ai/api/v1/chat/completions', [
        'model' => 'openai/gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Eres un optimizador de rutas para picking logístico'],
            ['role' => 'user', 'content' => $param1]
        ]
    ]);

    if ($respuesta->failed()) {
        return "Error al contactar IA: " . $respuesta->body();
    }

     $content = $respuesta->json('choices.0.message.content');

        // Paso 4: Retornar la respuesta al cliente MCP
        return $content;
    }
}
