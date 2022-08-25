<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class AnalyticsLoyaltyDistributionController extends Controller
{
    public function index(){

        $venues = OmayaVenue::get();
        return view('v1.admin.analytics.loyalty.index', compact('venues'));
    }
}
