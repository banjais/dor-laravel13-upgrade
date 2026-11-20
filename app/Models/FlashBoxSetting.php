<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashBoxSetting extends Model
{
    protected $table = 'flash_box_settings';

    protected $fillable = [
        'scope',
        'animation_enabled',
        'autoplay_enabled',
        'autoplay_speed',
        'animation_effect',
        'pause_on_hover',
        'arrows_enabled',
        'dots_enabled',
        'mobile_animation_enabled',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'animation_enabled' => 'boolean',
            'autoplay_enabled' => 'boolean',
            'autoplay_speed' => 'integer',
            'pause_on_hover' => 'boolean',
            'arrows_enabled' => 'boolean',
            'dots_enabled' => 'boolean',
            'mobile_animation_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['scope' => 'global'],
            [
                'animation_enabled' => true,
                'autoplay_enabled' => true,
                'autoplay_speed' => 4000,
                'animation_effect' => 'slide',
                'pause_on_hover' => true,
                'arrows_enabled' => true,
                'dots_enabled' => false,
                'mobile_animation_enabled' => true,
            ]
        );
    }
}
