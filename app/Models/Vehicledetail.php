<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicledetail extends Model
{
    protected $table = 'vehicle_detail';

	protected $fillable = array('vehicle_type','vehicle_number','condition');
}
