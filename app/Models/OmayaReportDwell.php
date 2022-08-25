<?php

namespace App\Models;

use App\Supports\Shared\SetTimeZone;
use App\Supports\Shared\WhereTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmayaReportDwell extends Model
{
    use HasFactory, WhereTenant, SetTimeZone;

    protected $guarded = [];
    // protected $fillable = [
    //     'tenant_id',
    //     'report_date',
    //     'location_uid',
    //     'venue_uid',
    //     'zone_uid',
    //     'total_dwell',
    //     'total_dwell',
    //     'total_dwell',
    //     'total_dwell_engaged',
    //     'dwell_15',
    //     'dwell_30',
    //     'dwell_60',
    //     'dwell_120',
    //     'dwell_240',
    //     'dwell_480',
    //     'dwell_more',
    // ];
}
