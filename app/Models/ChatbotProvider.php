<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotProvider extends Model
{
    protected $table = 'chatbot_providers';

    protected $fillable = [
        'provider_code', 'provider_name', 'provider_type',
        'api_format', 'model', 'base_url', 'api_key',
        'priority', 'is_enabled', 'timeout_seconds',
        'max_retries', 'health_status', 'last_health_check_at',
        'quota_reset_at', 'last_error', 'metadata',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_enabled' => 'boolean',
            'priority' => 'integer',
            'timeout_seconds' => 'integer',
            'max_retries' => 'integer',
            'last_health_check_at' => 'datetime',
            'quota_reset_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(ChatbotUsageLog::class, 'provider_id');
    }
}
