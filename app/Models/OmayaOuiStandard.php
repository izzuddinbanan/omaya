<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaOuiStandard extends Model
{
    use HasFactory;


    protected $fillable = [
        'mac_address',
        'vendor',
        'created_at',
        'updated_at',
    ];
}
