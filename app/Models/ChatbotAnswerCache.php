<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotAnswerCache extends Model
{
    protected $table = 'chatbot_answer_cache';

    protected $fillable = [
        'cache_key', 'question_hash', 'normalized_question',
        'language_code', 'office_id', 'answer',
        'source_references', 'index_version', 'is_public',
        'hit_count', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'source_references' => 'array',
            'is_public' => 'boolean',
            'hit_count' => 'integer',
            'expires_at' => 'datetime',
        ];
    }
}
