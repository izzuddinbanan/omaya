<?php

namespace App\Http\Controllers\v1\Admin\Monitors;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use Illuminate\Http\Request;

class MonitorDeviceApController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {


            $total["all-active"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_controllers WHERE tenant_id = '" . session("tenant_id") ."' AND is_active = '1'"))[0]->count;


            // COUNT ALL DEVICE 
            $total["all"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_controllers WHERE tenant_id = '" . session("tenant_id") ."' "))[0]->count;

            $total["online"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_controllers WHERE tenant_id = '" . session("tenant_id") ."' AND status = 'active'"))[0]->count;

            $total["no-new"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_controllers WHERE tenant_id = '" . session("tenant_id") ."' AND status = 'no new packet'"))[0]->count;

            $total["offline"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_controllers WHERE tenant_id = '" . session("tenant_id") ."' AND (status = 'offline' OR status IS NULL)"))[0]->count;

            return ["total" => $total];



        }else {


            // $ip =   "192.168.0.82";
            // exec("ping -c 1 -W 5 192.168.0.38", $output, $status);
            // dd($status);
            // exit();
            $devices = OmayaDeviceController::get();

            return view('v1.admin.monitors.device-ap.index', compact('devices'));
        }
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
