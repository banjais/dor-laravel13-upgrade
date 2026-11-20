<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteThemeSetting extends Model
{
    protected $table = 'website_theme_settings';

    protected $fillable = [
        'page_background',
        'card_background',
        'header_background',
        'nav_background',
        'accent_color',
        'footer_background',
        'text_color',
        'border_color',
        'updated_by',
    ];

    public const DEFAULTS = [
        'page_background' => '#F5F6F4',
        'card_background' => '#FFFFFF',
        'header_background' => '#142D4E',
        'nav_background' => '#1769AA',
        'accent_color' => '#8B2635',
        'footer_background' => '#263746',
        'text_color' => '#263238',
        'border_color' => '#E5E8E6',
    ];

    /**
     * Return the single global website theme.
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['scope' => 'global'],
            self::DEFAULTS
        );
    }

    /**
     * Restore the global theme to its default colours.
     */
    public static function resetToDefaults(?int $updatedBy = null): self
    {
        $theme = static::current();

        $theme->fill(self::DEFAULTS);
        $theme->updated_by = $updatedBy;
        $theme->save();

        return $theme;
    }
}
