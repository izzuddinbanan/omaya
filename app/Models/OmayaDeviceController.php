<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaDeviceController extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;

    protected $fillable = [
        'tenant_id',
        'location_uid',
        'venue_uid',
        'zone_uid',
        'device_uid',
        'name',
        'mac_address',
        'mac_address_separator',
        'device_type',
        'position_x',
        'position_y',
        'rssi_min',
        'rssi_max',
        'rssi_min_ble',
        'rssi_max_ble',
        'dwell_time',
        'is_default_setting',
        'last_seen_at',
        'status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $appends = [

        'status_color',

    ];

    public function zone(){
        return $this->belongsTo(OmayaZone::class, 'zone_uid', 'zone_uid');
    }

    public function venue(){
        return $this->belongsTo(OmayaVenue::class, 'venue_uid', 'venue_uid');
    }

    public function location(){
        return $this->belongsTo(OmayaLocation::class, 'location_uid', 'location_uid');
    }

    public function getStatusColorAttribute()
    {
        if($this->status == 'active') {

            return "success";
            
        }elseif($this->status == 'no new packet'){

            return "secondary";

        }else {

            return "danger";
        }
    }
}
