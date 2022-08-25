<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaCloud extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'is_active',
        'license_key',
        'name',
        'address',
        'phone',
        'email',
        'timezone',
        'delete_log',
        'smtp_is_active',
        'smtp_host',
        'smtp_port',
        'smtp_auth',
        'smtp_username',
        'smtp_password',
        'smtp_from_email',
        'smtp_from_name',
        'location_image',
        'location_image_width',
        'location_image_height',
        'is_filter_oui',
        'is_filter_mac_random',
        'is_filter_dwell_time',
        'remove_dwell_time',
        'created_by',
        'updated_by',
        'expired_at',
    ];


    protected $appends = [

        'location_image_url',
        'thumbnail_location_image_url',

    ];

    public function getLocationImageUrlAttribute() {

        if($this->location_image){

            return url('storage/tenants/' . session('tenant_id') . '/locations/' . $this->location_image);
        }
    }


    public function getThumbnailLocationImageUrlAttribute() {

        if($this->location_image){
            return url('storage/tenants/' . session('tenant_id') . '/locations/thumbnails/' . removeStringAfterCharacters($this->location_image) .'.jpg');
        }
        
    }
}
