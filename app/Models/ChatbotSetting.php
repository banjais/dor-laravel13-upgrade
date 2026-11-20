<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $table = 'chatbot_settings';

    protected $fillable = [
        'scope', 'enabled', 'bot_name', 'welcome_message',
        'offline_message', 'response_language', 'position',
        'panel_color', 'header_color', 'user_message_color',
        'bot_message_color', 'text_color', 'ai_provider',
        'ai_model', 'api_key', 'api_base_url', 'api_format',
        'appsheet_url', 'google_sheet_url', 'website_base_url',
        'website_index_enabled',
        'external_api_enabled',
        'primary_enabled', 'primary_provider', 'primary_model',
        'primary_base_url', 'primary_api_key',
        'secondary_enabled', 'secondary_provider', 'secondary_model',
        'secondary_base_url', 'secondary_api_key',
        'fallback_enabled', 'fallback_provider', 'fallback_model',
        'fallback_base_url', 'fallback_api_key',
        'cache_enabled', 'cache_exact_match_enabled', 'cache_ttl_minutes',
        'internal_ai_enabled', 'hardware_mode', 'gpu_enabled',
        'gpu_server_url', 'gpu_model',
        'supported_by_name', 'supported_by_email', 'supported_by_visible', 'provider_info_visible',
        'voice_enabled',
        'speech_input_enabled', 'voice_output_enabled',
        'voice_type', 'custom_voice', 'voice_speed', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'voice_enabled' => 'boolean',
            'internal_ai_enabled' => 'boolean',
            'gpu_enabled' => 'boolean',
            'supported_by_visible' => 'boolean',
            'provider_info_visible' => 'boolean',
            'speech_input_enabled' => 'boolean',
            'voice_output_enabled' => 'boolean',
            'voice_speed' => 'float',
            'cache_ttl_minutes' => 'integer',
            'api_key' => 'encrypted',
            'primary_api_key' => 'encrypted',
            'secondary_api_key' => 'encrypted',
            'fallback_api_key' => 'encrypted',
            'external_api_enabled' => 'boolean',
            'primary_enabled' => 'boolean',
            'secondary_enabled' => 'boolean',
            'fallback_enabled' => 'boolean',
            'cache_enabled' => 'boolean',
            'cache_exact_match_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['scope' => 'global'],
            [
                'enabled' => false,
                'bot_name' => 'DoR Assistant',
                'welcome_message' => null,
                'offline_message' => null,
                'response_language' => 'both',
                'position' => 'bottom_right',
                'panel_color' => '#FFFFFF',
                'header_color' => '#142D4E',
                'user_message_color' => '#1769AA',
                'bot_message_color' => '#F1F4F7',
                'text_color' => '#263238',
                'ai_provider' => 'openai',
                'ai_model' => 'gpt-4o-mini',
                'api_format' => 'openai_compatible',
                'website_base_url' => 'https://dor.gov.np',
                'website_index_enabled' => true,
                'internal_ai_enabled' => false,
                'hardware_mode' => 'cpu',
                'gpu_enabled' => false,
                'supported_by_visible' => false,
                'provider_info_visible' => false,
                'external_api_enabled' => false,
                'primary_enabled' => false,
                'secondary_enabled' => false,
                'fallback_enabled' => false,
                'cache_enabled' => true,
                'cache_exact_match_enabled' => true,
                'cache_ttl_minutes' => 1440,
                'voice_enabled' => false,
                'speech_input_enabled' => false,
                'voice_output_enabled' => false,
                'voice_type' => 'female',
                'voice_speed' => 1.00,
            ]
        );
    }
}
