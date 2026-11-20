<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotKnowledgeSource extends Model
{
    protected $table = 'chatbot_knowledge_sources';

    protected $fillable = [
        'office_id', 'source_type', 'name', 'source_url',
        'storage_disk', 'storage_path', 'external_id',
        'auth_config', 'language_code', 'status',
        'is_enabled', 'is_public', 'last_checked_at',
        'last_indexed_at', 'content_checksum', 'source_version',
        'last_error', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'auth_config' => 'encrypted:array',
            'is_enabled' => 'boolean',
            'is_public' => 'boolean',
            'last_checked_at' => 'datetime',
            'last_indexed_at' => 'datetime',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ChatbotKnowledgeDocument::class, 'source_id');
    }
}
