<?php

namespace App\Http\Controllers\v1\Admin\Monitors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorSchedulerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $schedulers = [];
        $omy_cache = redisCache();

        foreach (["REPORT:DWELL" => "Every Minute", "REPORT:GENERAL" => "Every Minute", "REPORT:HEATMAP" => "Every Minute", "OMAYA:MAIN" => "Every Minute", "OMAYA:SERVICE" => "Every Minute", "REPORT:DEVICE-CONTROLLER" => "Every Minute", "REPORT:CROSS-VISIT" => "Every Minute", "OMAYA:PRE-REPORT" => "Hourly At Minute 45", "OMAYA:BLACKLIST" => "Every Minute", "LOG:CLEAR" => "Daily"] as $value => $minute) {
            
            $schedulers[$value]["name"]  = str_replace(":", " ", $value);
            $schedulers[$value]["run"]   = $minute;

            $keys = $omy_cache->keys("OMAYA:SCHEDULER:{$value}*");
            
            if(empty($keys)) continue;
            $data = $omy_cache->hGetAll($keys[0]);

            if(!empty($data)){

                $schedulers[$value]["last_run_start"] = $data['start'];
                $schedulers[$value]["last_run_end"]   = $data['end'];

                $temp = strtotime($data['end']) - strtotime($data['start']);

                $schedulers[$value]["time_taken"]     =  $temp . "(s)";

                $schedulers[$value]["status"]     = ($temp < 120) ? "active" : "inactive";
            } else {


                $schedulers[$value]["last_run_start"] = "-";
                $schedulers[$value]["last_run_end"]   = "-";
                $schedulers[$value]["time_taken"]     = "-";
                $schedulers[$value]["status"]     = "inactive";

            }

        }


        return view('v1.admin.monitors.schedulers.index', compact('schedulers'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
