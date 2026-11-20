<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalTool extends Model
{
    protected $table = 'external_tools';
    protected $fillable = array('tool_url','tool_icon', 'display_text','hover_text');
}
