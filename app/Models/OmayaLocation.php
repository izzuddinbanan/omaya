<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaLocation extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;

    protected $fillable = [
    	'tenant_id',
    	'location_uid',
    	'name',
    	'address',
    	'remark',
    	'created_by',
    	'updated_by',
    ];

    public function venues(){
        return $this->hasMany(OmayaVenue::class, 'location_uid', 'location_uid');
    }
}
