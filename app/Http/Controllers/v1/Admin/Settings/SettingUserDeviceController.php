<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MacAlias;
use App\Models\MacFilter;
use App\Models\OmayaVenue;
use Illuminate\Http\Request;

use DataTables;

class SettingUserDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.settings.user_device.index');
    }

    public function dataTable(Request $request)
    {

        if ($request->ajax()) {

            $raw_count = 1;

            $data = MacAlias::orderByDesc('updated_at');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $action_btn  = "";
                    $action_btn .= able_to("setting", "user-device", "r") ? editCustomButton(route('admin.setting.device_user.edit', [$data->mac_alias_uid])) : "";
                    $action_btn .= able_to("setting", "user-device", "rw") ? deleteCustomButton(route('admin.setting.device_user.destroy', [$data->mac_alias_uid])) : "";
        
                    return actionCustomButton($action_btn);

                })
                ->addColumn('responsive_id', function($data){
                    $responsive_id = "";
                    return $responsive_id;

                })
                ->rawColumns(['action', 'raw_count', 'responsive_id'])
                ->make(true);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $venue = OmayaVenue::get();
        return view('v1.admin.settings.user_device.create', compact('venue'));

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

            //START VALIDATION
            $rules = [
                'alias'         => 'required|min:4|max:50',
                'mac_address'   => 'required|min:1|max:50|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                'type'          => 'required',
            ];

            
            $this->validate($request, $rules);
            unset($rules);

            if($request->type != 'user'){
                if(!isset($request->venue)){
                    return back()->withErrors(['venue' => 'Venue field is required'])->withInput();
                }
            }

            if(MacAlias::where('alias', $request->alias)->first()){
                return back()->withErrors(['username' => 'Device Alias has been taken'])->withInput();
            }

            if(MacAlias::where('mac_address', $request->mac_address)->first()){
                return back()->withErrors(['username' => 'Mac Address has been taken'])->withInput();
            }

            //END VALIDATION

            $user = \Auth::user();

            while(true){

                $uid = randomStringId(8);
                
                if(!MacAlias::where('mac_alias_uid', $uid)->first()){
                    
                    break;
                }

            }

            $user_device = MacAlias::create([
                'mac_alias_uid' => $uid,
                'tenant_id'     => session('tenant_id'),
                'mac_address'   => $request->mac_address,
                'alias'         => $request->alias,
                'type'          => $request->type,
                'created_by'    => $user->id,
                'updated_by'    => $user->id,
            ]);

            if($user_device){
                if($request->type != 'user'){
                    if(is_array($request->venue) && count($request->venue)){
                        foreach($request->venue as $venue_uid){
                            MacFilter::create([
                                "venue_uid"         => $venue_uid,
                                "mac_address"       => $request->mac_address,
                                "tenant_id"         => session('tenant_id'),
                                "last_update"       => date("Y-m-d H:00:00"),
                                "status"            => "5",
                                "final_detection"   => $request->type
                            ]);
                        }
                    }
                }
            }


        }
        catch (ValidationException $e) {
            return redirect(route('admin.setting.device_user.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.device_user.edit', $uid))
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
        $venue  = OmayaVenue::get();
        $device = MacAlias::where('mac_alias_uid', $id)->first();

        $filter = MacFilter::where('mac_address', $device->mac_address)->pluck('venue_uid')->toArray();
        return view('v1.admin.settings.user_device.edit', compact('venue', 'device', 'filter'));
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

        if(!$user_device =MacAlias::where('mac_alias_uid', $id)->first()){

            return redirect(route('admin.setting.device_user.index'))
                    ->withErrors(trans('alert.record-not-found'));

        }

        try{

            //START VALIDATION
            $rules = [
                'alias'         => 'required|min:4|max:50',
                'mac_address'   => 'required|min:1|max:50|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                'type'          => 'required',
            ];

            
            $this->validate($request, $rules);
            unset($rules);

            if($request->type != 'user'){
                if(!isset($request->venue)){
                    return back()->withErrors(['venue' => 'Venue field is required'])->withInput();
                }
            }

            if(MacAlias::where('alias', $request->alias)->where('mac_alias_uid', '!=', $id)->first()){
                return back()->withErrors(['username' => 'Device Alias has been taken'])->withInput();
            }

            if(MacAlias::where('mac_address', $request->mac_address)->where('mac_alias_uid', '!=', $id)->first()){
                return back()->withErrors(['username' => 'Mac Address has been taken'])->withInput();
            }

            //END VALIDATION

            $user = \Auth::user();

            //delete first existing data with current mac address to prevent changing mac address
            $filter = MacFilter::where('mac_address', $user_device['mac_address']);
            if(count($filter->get()) > 0){
                $filter->delete();                        
            }

            $user_device['mac_address'] = $request->mac_address;
            $user_device['alias']       = $request->alias;
            $user_device['type']        = $request->type;
            $user_device['updated_by']  = $user->id;
            
            $user_device->save();

            if($user_device){
                if($request->type != 'user'){
                    if(is_array($request->venue) && count($request->venue)){
    
                        foreach($request->venue as $venue_uid){
                            MacFilter::create([
                                "venue_uid"         => $venue_uid,
                                "mac_address"       => $request->mac_address,
                                "tenant_id"         => session('tenant_id'),
                                "last_update"       => date("Y-m-d H:00:00"),
                                "status"            => "5",
                                "final_detection"   => $request->type
                            ]);
                        }
                    }
                }
            }


        }
        catch (ValidationException $e) {
            return redirect(route('admin.setting.device_user.edit', $id))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.device_user.edit', $id))
        ->withSuccess(trans('alert.success-create'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uid, Request $request)
    {
        if($request->ajax()){

            $check = MacAlias::where('mac_alias_uid', $uid);
            if(count($check->get()) == 0){
                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $checkFilter = MacFilter::where('mac_address', $check->first()->mac_address);
            if(count($checkFilter->get()) > 0){
                $checkFilter->delete();                        
            }
            
            $check->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }
}
