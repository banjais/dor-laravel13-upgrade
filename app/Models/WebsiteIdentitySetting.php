<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteIdentitySetting extends Model
{
    protected $table = 'website_identity_settings';

    protected $fillable = [
        'government_name_np',
        'government_name_en',
        'ministry_name_np',
        'ministry_name_en',
        'department_name_np',
        'department_name_en',
        'updated_by',
    ];

    public const DEFAULTS = [
        'government_name_np' => 'नेपाल सरकार',
        'government_name_en' => 'Government of Nepal',
        'ministry_name_np' => 'पूर्वाधार विकास मन्त्रालय',
        'ministry_name_en' => 'Ministry of Infrastructure Development',
        'department_name_np' => 'सडक विभाग',
        'department_name_en' => 'Department of Roads',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['scope' => 'global'],
            self::DEFAULTS
        );
    }
}
