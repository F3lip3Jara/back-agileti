<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'linear_issue_id',
        'title',
        'status',
        'agent',
        'prompt',
        'output',
        'logs'
    ];

    /**
     * Appends a log line to the task in a terminal-like format.
     */
    public function addLog(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[$timestamp] [$level] $message\n";
        $this->logs = ($this->logs ?? '') . $logLine;
        $this->save();
    }
}
