<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaVenue extends Model
{
    use HasFactory, SetTimeZone, WhereTenant;


    protected $fillable = [
    	'tenant_id',
    	'location_uid',
        'venue_uid',
    	'name',
    	'level',
    	'address',
    	'venue_zone',
    	'image',
    	'image_width',
    	'image_height',
    	'space_length_point',
    	'space_length_meter',
    	'space_length_px',
        'rssi_min',
        'rssi_max',
        'rssi_min_ble',
        'rssi_max_ble',
        'dwell_time',
    	'default_dashboard',
    	'created_by',
    	'updated_by',
    ];

    protected $appends = [

        'image_url',
        'thumbnail_image_url',

    ];

    public function location(){
        return $this->belongsTo(OmayaLocation::class, 'location_uid', 'location_uid');
    }

    public function zones(){
        return $this->hasMany(OmayaZone::class, 'venue_uid', 'venue_uid');
    }

	// public function device(){
 //        return $this->hasMany(VenueDev::class, 'venue_uid', 'venue_uid');
 //    }

	// public function map(){
 //        return $this->hasOne(OmayaVenueMap::class, 'venue_uid', 'venue_uid');
 //    }

	public function getImageUrlAttribute() {

        if($this->image){

            return url('storage/tenants/' . session('tenant_id') . '/venues/' . $this->image);
        }
    }


    public function getThumbnailImageUrlAttribute() {

        if($this->image){
            return url('storage/tenants/' . session('tenant_id') . '/venues/thumbnails/' . removeStringAfterCharacters($this->image) .'.jpg');
        }
        
    }
}
