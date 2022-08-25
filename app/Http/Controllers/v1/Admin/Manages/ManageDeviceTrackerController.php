<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceTracker;
use Illuminate\Http\Request;

class ManageDeviceTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = OmayaDeviceTracker::get();
        return view('v1.admin.manages.device-trackers.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('v1.admin.manages.device-trackers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{


            ###########
            ## START VALIDATION PART
            ###########

            $omy_rules = [
                'name'          => 'required|min:3|max:200',
                'mac_address'   => 'required|min:10|max:50|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];


            $mac_address = str_replace([':', '-'], '', $request->input('mac_address'));

            if(\DB::table('omaya_device_trackers')->where('mac_address', $mac_address)->where('tenant_id', session('tenant_id'))->first()) {

                $custom_errors = array_merge($custom_errors, ['mac_address' => 'Mac address already been used']);

            }

            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }

            unset($custom_errors);

            ############
            ## END VALIDATION PART
            ############


            
            do {

                $uid = randomStringId();

            } while (OmayaDeviceTracker::where('device_uid', $uid)->first());


            $omy_user = \Auth::user()->id;


            OmayaDeviceTracker::create([
                'tenant_id'                 => session('tenant_id'),
                'device_uid'                => $uid,
                'name'                      => $request->input('name'),
                'remarks'                   => $request->input('remarks'),
                'mac_address'               => strtoupper($mac_address),
                'mac_address_separator'     => strtoupper(str_replace("-", ":", $request->input('mac_address'))),
                'is_active'                 => $request->input('is_active') == true  ? true : false,
                'created_by'                => $omy_user,
                'updated_by'                => $omy_user,
            ]);


            unset($omy_user);


        }catch (ValidationException $e) {

            return redirect(route('admin.manage.device-tracker.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.device-tracker.edit', [$uid]))
            ->withSuccess(trans('alert.success-create'));
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

        if(!$device = OmayaDeviceTracker::where('device_uid', $id)->first()) {

            return redirect(route('admin.manage.device-tracker.index'))->withErrors(trans('alert.record-not-found'));

        }

        return view('v1.admin.manages.device-trackers.edit', compact('device'));
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
        
        if(!$device = OmayaDeviceTracker::where('device_uid', $id)->first()) {

            return redirect(route('admin.manage.device-tracker.index'))->withErrors(trans('alert.record-not-found'));

        }

        try{


            ###########
            ## START VALIDATION PART
            ###########

            $omy_rules = [
                'name'          => 'required|min:3|max:200',
                'mac_address'   => 'required|min:10|max:50|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            $mac_address = str_replace([':', '-'], '', $request->input('mac_address'));

            if(\DB::table('omaya_device_trackers')->where('mac_address', $mac_address)->where('device_uid', '!=', $id)->where('tenant_id', session('tenant_id'))->first()) {

                $custom_errors = array_merge($custom_errors, ['mac_address' => 'Mac address already been used']);

            }
          

            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }

            unset($custom_errors);

            ############
            ## END VALIDATION PART
            ############



            $omy_user = \Auth::user()->id;

            $device->update([
                'name'                      => $request->input('name'),
                'remarks'                   => $request->input('remarks'),
                'mac_address'               => strtoupper($mac_address),
                'mac_address_separator'     => strtoupper(str_replace("-", ":", $request->input('mac_address'))),
                'is_active'                 => $request->input('is_active') == true  ? true : false,
                'updated_by'                => $omy_user,

            ]);


            unset($omy_user);


        }catch (ValidationException $e) {

            return redirect(route('admin.manage.device-tracker.edit', [$id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.device-tracker.edit', [$id]))
            ->withSuccess(trans('alert.success-update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if($request->ajax()){


            if(!$omy_device = OmayaDeviceTracker::where('device_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $omy_device->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }
}
