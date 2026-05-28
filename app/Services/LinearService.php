<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinearService
{
    protected ?string $apiKey;
    protected string $apiUrl = 'https://api.linear.app/graphql';

    public function __construct()
    {
        $this->apiKey = env('LINEAR_API_KEY');
    }

    /**
     * Helper to make a raw GraphQL request to Linear.
     */
    protected function query(string $query, array $variables = []): array
    {
        if (empty($this->apiKey)) {
            Log::warning('Linear API Key is not configured in .env file.');
            return ['errors' => [['message' => 'Linear API key missing']]];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'query' => $query,
                'variables' => $variables
            ]);

            if ($response->failed()) {
                Log::error('Linear API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['errors' => [['message' => 'HTTP request failed: ' . $response->status()]]];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Linear API exception', ['message' => $e->getMessage()]);
            return ['errors' => [['message' => $e->getMessage()]]];
        }
    }

    /**
     * Retrieve issue details including workflow states of the team.
     */
    public function getIssue(string $issueKey): ?array
    {
        $gql = '
            query GetIssue($id: String!) {
                issue(id: $id) {
                    id
                    identifier
                    title
                    description
                    state {
                        id
                        name
                        type
                    }
                    team {
                        id
                        states {
                            id
                            name
                            type
                        }
                    }
                }
            }
        ';

        $res = $this->query($gql, ['id' => $issueKey]);

        if (isset($res['errors'])) {
            Log::error('Error fetching Linear issue ' . $issueKey, $res['errors']);
            return null;
        }

        return $res['data']['issue'] ?? null;
    }

    /**
     * Update the issue's workflow state.
     */
    public function updateIssueState(string $issueUuid, string $stateUuid): bool
    {
        $gql = '
            mutation UpdateIssue($id: UUID!, $stateId: UUID!) {
                issueUpdate(id: $id, input: { stateId: $stateId }) {
                    success
                    issue {
                        id
                        identifier
                    }
                }
            }
        ';

        $res = $this->query($gql, [
            'id' => $issueUuid,
            'stateId' => $stateUuid
        ]);

        if (isset($res['errors'])) {
            Log::error('Error updating Linear issue state', $res['errors']);
            return false;
        }

        return $res['data']['issueUpdate']['success'] ?? false;
    }

    /**
     * Add a comment to the issue.
     */
    public function addComment(string $issueUuid, string $commentBody): bool
    {
        $gql = '
            mutation CreateComment($issueId: UUID!, $body: String!) {
                commentCreate(input: { issueId: $issueId, body: $body }) {
                    success
                    comment {
                        id
                    }
                }
            }
        ';

        $res = $this->query($gql, [
            'issueId' => $issueUuid,
            'body' => $commentBody
        ]);

        if (isset($res['errors'])) {
            Log::error('Error creating Linear comment', $res['errors']);
            return false;
        }

        return $res['data']['commentCreate']['success'] ?? false;
    }

    /**
     * Helper to transition an issue to a target state by name (e.g. "Done", "In Review")
     */
    public function transitionToState(string $issueKey, string $targetStateName): bool
    {
        $issue = $this->getIssue($issueKey);
        if (!$issue) {
            Log::error("Cannot transition issue $issueKey: Issue not found.");
            return false;
        }

        $issueUuid = $issue['id'];
        $states = $issue['team']['states'] ?? [];

        $targetStateUuid = null;
        foreach ($states as $state) {
            if (strcasecmp($state['name'], $targetStateName) === 0) {
                $targetStateUuid = $state['id'];
                break;
            }
        }

        if (!$targetStateUuid) {
            Log::warning("Workflow state '$targetStateName' not found for issue $issueKey. Available: " . implode(', ', array_column($states, 'name')));
            return false;
        }

        return $this->updateIssueState($issueUuid, $targetStateUuid);
    }
}
