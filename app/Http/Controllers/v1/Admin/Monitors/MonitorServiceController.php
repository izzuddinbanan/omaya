<?php

namespace App\Http\Controllers\v1\Admin\Monitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

                
        $services = @file_get_contents("/var/www/omaya/public/storage/omaya-service.json");

        $services = json_decode($services, true);
        if(empty($services)) $services = ["services" => []];

        return view('v1.admin.monitors.services.index', compact('services'));
    }
}
