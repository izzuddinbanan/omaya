<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaZone extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;


    protected $fillable = [
    	'tenant_id',
    	'location_uid',
        'venue_uid',
        'zone_uid',
    	'name',
    	'remark',
    	'created_by',
    	'updated_by',
    ];

    public function location(){
        return $this->belongsTo(OmayaLocation::class, 'location_uid', 'location_uid');
    }

    public function venue(){
        return $this->belongsTo(OmayaVenue::class, 'venue_uid', 'venue_uid');
    }

}
