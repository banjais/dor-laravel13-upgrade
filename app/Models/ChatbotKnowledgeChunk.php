<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotKnowledgeChunk extends Model
{
    protected $table = 'chatbot_knowledge_chunks';

    protected $fillable = [
        'document_id', 'source_id', 'office_id', 'language_id',
        'chunk_number', 'chunk_text', 'token_count',
        'chunk_hash', 'status',
    ];

    protected function casts(): array
    {
        return [
            'chunk_number' => 'integer',
            'token_count' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotKnowledgeDocument::class, 'document_id');
    }
}
