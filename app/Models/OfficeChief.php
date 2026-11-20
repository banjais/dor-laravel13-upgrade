<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeChief extends Model
{
    protected $fillable = array( 'is_active', 'is_deleted', 'created_by', 'updated_by', 'office_id');

    protected $table = 'office_chiefs';

    public function details()
    {
        return $this->hasMany(OfficeChiefDetail::class, 'chief_id');
    }
}
