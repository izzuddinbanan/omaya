<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

class OmayaUser extends  Authenticatable
{
    use Notifiable;
    use HasFactory, SetTimeZone;
    

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $fillable = [
        'tenant_id',
        'user_uid',
        'email',
        'username',
        'password',
        'allowed_venue_id',
        'role',
        'permission',
        'reset_key',
        'web_mode',
        'location_uid',
        'venue_uid',
        'created_by',
        'updated_by',
    ];


    public function getImageUrlAttribute() {

        if($this->photo){

            return url('storage/tenants/' . session('tenant_id') . '/user/' . $this->image);
        }
    }


    public function getThumbnailImageUrlAttribute() {

        if($this->photo){
            return url('storage/tenants/' . session('tenant_id') . '/user/thumbnails/' . removeStringAfterCharacters($this->photo) .'.jpg');
        }
        
    }
}
