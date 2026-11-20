<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotIntegration extends Model
{
    protected $table = 'chatbot_integrations';

    protected $fillable = [
        'integration_code',
        'display_name',
        'integration_type',
        'credentials',
        'settings',
        'is_enabled',
        'health_status',
        'last_tested_at',
        'last_success_at',
        'last_error',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'settings' => 'array',
            'is_enabled' => 'boolean',
            'last_tested_at' => 'datetime',
            'last_success_at' => 'datetime',
        ];
    }
}
