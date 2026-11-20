<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotKnowledgeDocument extends Model
{
    protected $table = 'chatbot_knowledge_documents';

    protected $fillable = [
        'source_id', 'office_id', 'language_id', 'document_type',
        'source_key', 'title', 'source_url', 'storage_disk',
        'storage_path', 'mime_type', 'file_size', 'checksum',
        'source_updated_at', 'extracted_text', 'extraction_method',
        'parser_status', 'indexed_at', 'last_error', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'source_updated_at' => 'datetime',
            'indexed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ChatbotKnowledgeSource::class, 'source_id');
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(ChatbotKnowledgeChunk::class, 'document_id');
    }
}
