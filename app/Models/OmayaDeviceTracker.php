<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaDeviceTracker extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;


    protected $fillable = [
        'tenant_id',
        'device_uid',
        'name',
        'mac_address',
        'mac_address_separator',
        'remarks',
        'last_seen_at',
        'last_location_uid',
        'last_venue_uid',
        'last_zone_uid',
        'is_active',
        'is_allocated',
        'created_by',
        'updated_by',
    ];

    public function entity(){
        return $this->hasOne(OmayaEntity::class, 'device_tracker_uid', 'device_uid');
    }
}
