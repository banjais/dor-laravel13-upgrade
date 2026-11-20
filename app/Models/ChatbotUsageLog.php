<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotUsageLog extends Model
{
    protected $table = 'chatbot_usage_logs';

    public $timestamps = false;

    protected $fillable = [
        'provider_id', 'office_id', 'request_id',
        'provider_slot', 'model', 'status', 'error_code', 'http_status',
        'latency_ms', 'prompt_tokens', 'completion_tokens',
        'total_tokens', 'estimated_cost', 'request_hash',
        'safe_error_message', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latency_ms' => 'integer',
            'http_status' => 'integer',
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
            'total_tokens' => 'integer',
            'estimated_cost' => 'decimal:6',
            'created_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ChatbotProvider::class, 'provider_id');
    }
}
