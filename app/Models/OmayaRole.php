<?php

namespace App\Models;

use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaRole extends Model
{
    use HasFactory, WhereTenant;

    protected $fillable = [
    	'tenant_id',
    	'name',
		'allowed_venue_id',
    	'module_id',
    	'created_by',
    	'updated_by',
    ];
}
