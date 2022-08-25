<?php

namespace App\Http\Controllers\v1\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class AnalyticsEntryExitController extends Controller
{
    public function index(){

        $venues = OmayaZone::get();
        return view('v1.admin.analytics.entry_exit.index', compact('venues'));

    }
}
