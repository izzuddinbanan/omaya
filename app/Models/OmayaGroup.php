<?php

namespace App\Models;


use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaGroup extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;

    protected $guarded = [];

}
