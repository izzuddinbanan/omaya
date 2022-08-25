<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaNotification extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;

    protected $guarded = [];


    function location(){
        return $this->belongsTo(OmayaLocation::class, 'location_uid', 'location_uid');   
    }

    function venue(){
        return $this->belongsTo(OmayaVenue::class, 'venue_uid', 'venue_uid');   
    }

    function zone(){
        return $this->belongsTo(OmayaZone::class, 'zone_uid', 'zone_uid');   
    }

    function entity(){
        return $this->belongsTo(OmayaEntity::class, 'entity_uid', 'entity_uid');   
    }

    function controller(){
        return $this->belongsTo(OmayaDeviceController::class, 'device_controller_uid', 'device_uid');   
    }

    function tracker(){
        return $this->belongsTo(OmayaDeviceTracker::class, 'device_tracker_uid', 'device_uid');   
    }


    function rule(){
        return $this->belongsTo(OmayaRule::class, 'rule_uid', 'rule_uid');   
    }

}
