<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class AnalyticsCrossPathController extends Controller
{
    public function index(){

        $venues = OmayaZone::get();

        return view('v1.admin.analytics.cross_path.index', compact('venues'));
    }
}
