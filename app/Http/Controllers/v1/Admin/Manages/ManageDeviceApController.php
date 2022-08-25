<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class ManageDeviceApController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = OmayaDeviceController::with('venue')->get();
        return view('v1.admin.manages.device-ap.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $locations = OmayaLocation::select('name', 'location_uid')->orderBy('name')->get();
        return view('v1.admin.manages.device-ap.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // return $request->input();
        try{


            ###########
            ## START VALIDATION PART
            ###########

            $omy_rules = [
                'name'          => 'required|min:3|max:200',
                'location'      => 'required|exists:omaya_locations,location_uid',
                'venue'         => 'required|exists:omaya_venues,venue_uid',
                'type'          => 'required',
                'zone'          => 'nullable|exists:omaya_zones,zone_uid',
                'mac_address'   => 'required|distinct|min:10|max:50|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }

            $mac_address = str_replace([':', '-'], '', $request->input('mac_address'));

            if(\DB::table('omaya_device_controllers')->where('mac_address', $mac_address)->first()) {

                $custom_errors = array_merge($custom_errors, ['mac_address' => 'Mac address already been used']);

            }
          
            if(!$omy_venue = OmayaVenue::where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->first()){

                $custom_errors = array_merge($custom_errors, ['venue' => 'Venue selected is not valid']);

            }

            if($request->input('zone_uid')) {

                if(!OmayaZone::where('venue_uid', $request->input('venue'))->where('zone_uid', $request->input('zone'))->first()){

                    $custom_errors = array_merge($custom_errors, ['zone' => 'Zone selected is not valid']);

                }
            }

            if(!array_key_exists($request->input('type'), config('custom.access_point_type'))){
                return back()->withErrors(['type' => 'The selected type does not exist.'])->withInput();
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

            } while (OmayaDeviceController::where('device_uid', $uid)->first());


            $omy_user = \Auth::user()->id;


            OmayaDeviceController::create([
                'tenant_id'                 => session('tenant_id'),
                'location_uid'              => $request->input('location'),
                'venue_uid'                 => $request->input('venue'),
                'zone_uid'                  => $request->input('zone'),
                'device_uid'                => $uid,
                'device_type'               => $request->input('type'),
                'name'                      => $request->input('name'),
                'mac_address'               => strtoupper($mac_address),
                'mac_address_separator'     => strtoupper(str_replace("-", ":", $request->input('mac_address'))),
                'rssi_min'                  => $request->input('enable') == true ? $omy_venue->rssi_min : $request->input('rssi_min'),
                'rssi_max'                  => $request->input('enable') == true ? $omy_venue->rssi_max : $request->input('rssi_enter'),
                'rssi_min_ble'              => $request->input('enable') == true ? $omy_venue->rssi_min_ble : $request->input('rssi_min_ble'),
                'rssi_max_ble'              => $request->input('enable') == true ? $omy_venue->rssi_max_ble : $request->input('rssi_enter_ble'),
                'dwell_time'                => $omy_venue->dwell_time,
                'is_default_setting'        => $request->input('enable') == true  ? true : false,
                'is_active'                 => $request->input('is_active') == true  ? true : false,
                'created_by'                => $omy_user,
                'updated_by'                => $omy_user,
            ]);


            unset($omy_user);


        }catch (ValidationException $e) {

            return redirect(route('admin.manage.device-ap.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.device-ap.edit', [$uid]))
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
        if(!$device = OmayaDeviceController::where('device_uid', $id)->first()) {

            return redirect(route('admin.manage.device-ap.index'))->withErrors(trans('alert.record-not-found'));

        }

        $locations = OmayaLocation::select('name', 'location_uid')->orderBy('name')->get();

        return view('v1.admin.manages.device-ap.edit', compact('locations', 'device'));
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

        if(!$device = OmayaDeviceController::where('device_uid', $id)->first()) {

            return redirect(route('admin.manage.device-ap.index'))->withErrors(trans('alert.record-not-found'));

        }

        try{


            ###########
            ## START VALIDATION PART
            ###########

            $omy_rules = [
                'name'          => 'required|min:3|max:200',
                'location'      => 'required|exists:omaya_locations,location_uid',
                'venue'         => 'required|exists:omaya_venues,venue_uid',
                'type'          => 'required',
                'zone'          => 'nullable|exists:omaya_zones,zone_uid',
                'mac_address'   => 'required|distinct|min:10|max:50|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }

            $mac_address = str_replace([':', '-'], '', $request->input('mac_address'));

            if(\DB::table('omaya_device_controllers')->where('mac_address', $mac_address)->where('device_uid', '!=', $id)->first()) {

                $custom_errors = array_merge($custom_errors, ['mac_address' => 'Mac address already been used']);

            }
          
            if(!$omy_venue = OmayaVenue::where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->first()){

                $custom_errors = array_merge($custom_errors, ['venue' => 'Venue selected is not valid']);

            }

            if($request->input('zone_uid')) {

                if(!OmayaZone::where('venue_uid', $request->input('venue'))->where('zone_uid', $request->input('zone'))->first()){

                    $custom_errors = array_merge($custom_errors, ['zone' => 'Zone selected is not valid']);

                }
            }

            if(!array_key_exists($request->input('type'), config('custom.access_point_type'))){
                return back()->withErrors(['type' => 'The selected type does not exist.'])->withInput();
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
                'location_uid'              => $request->input('location'),
                'venue_uid'                 => $request->input('venue'),
                'zone_uid'                  => $request->input('zone'),
                'device_type'               => $request->input('type'),
                'name'                      => $request->input('name'),
                'mac_address'               => strtoupper($mac_address),
                'mac_address_separator'     => strtoupper($request->input('mac_address')),
                'rssi_min'                  => $request->input('enable') == true ? $omy_venue->rssi_min : $request->input('rssi_min'),
                'rssi_max'                  => $request->input('enable') == true ? $omy_venue->rssi_max : $request->input('rssi_enter'),
                'rssi_min_ble'              => $request->input('enable') == true ? $omy_venue->rssi_min_ble : $request->input('rssi_min_ble'),
                'rssi_max_ble'              => $request->input('enable') == true ? $omy_venue->rssi_max_ble : $request->input('rssi_enter_ble'),
                'dwell_time'                => $omy_venue->dwell_time,
                'is_default_setting'        => $request->input('enable') == true  ? true : false,
                'is_active'                 => $request->input('is_active') == true  ? true : false,
                'updated_by'                => $omy_user,
            ]);


            unset($omy_user);


        }catch (ValidationException $e) {

            return redirect(route('admin.manage.device-ap.edit', [$id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.device-ap.edit', [$id]))
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


            if(!$omy_device = OmayaDeviceController::where('device_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $omy_device->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }


    public function exportFile(Request $request)
    {

        $fileName = 'template.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );


        $columns = array('Name', 'MAC Address', 'Type', 'Location', 'Venue', 'Zone');
      
        $name = array('Name AP 1','Name AP 2', 'Name AP 3');
        $mac_address = array('D4:45:D9:55:01:76','F3:5B:BF:C5:B6:70', 'D4:45:D9:55:01:77');
        $type = array('huawei','cambium', 'meraki');
        $location = array('location 1','location 2', 'location 3');
        $venue = array('venue 1','venue 2', 'venue 3');
        $zone = array('zone 1','zone 2', 'zone 3');

        $callback = function () use ($columns, $location,$venue,$zone,$name, $mac_address, $type) {

            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($location as $key => $loc){
                
                $row['Name']           = $name[$key];
                $row['MAC Address']    = $mac_address[$key];
                $row['Type']           = $type[$key];
                $row['Location']       = $loc;
                $row['Venue']          = $venue[$key];
                $row['Zone']           = $zone[$key];

                fputcsv($file, array($row['Name'], $row['MAC Address'], $row['Type'], $row['Location'], $row['Venue'], $row['Zone']));
            }


            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    
    }


    public function importFile(){

        return view('v1.admin.manages.device-ap.import');
    }

    public function insertFile(Request $request)
    {

        // dd($request->all());
        try {

            ######################
            ## START VALIDATION PART
            ######################

            $rules = [
                'upload_file'   => 'required|file|max:5000',
            ];

            $this->validate($request, $rules);
            unset($rules);

            ####################
            ## END VALIDATION PART
            ####################

            $user = \Auth::user();


            $error_data     = [];
            $success_data   = 0;


            if ($request->hasFile('upload_file')) {


                $file_data = fopen($request->file('upload_file'), "r") or exit("Unable to open the file!");


                $column = fgetcsv($file_data);

                while ($line = fgetcsv($file_data)) {

                    $rowData[] = $line;
                }



                $rules = [

                    0          => 'min:4|max:50|required',
                    1          => 'min:12|max:50|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/|required',
                    2          => 'min:4|max:50||required',
                    3          => 'required',
                    4          => 'required',
                    5          => 'required',

                ];

                $messages = [

                    "0.min"           => "The length of tag name must not less than :min",
                    '0.max'           => "The maximum length of tag name must not exceed :max",
                    '0.required'      => "Device name must not be empty!",
                    "1.min"           => "The length of MAC Address must not less than :min",
                    '1.max'           => "The maximum length of MAC Address must not exceed :max",
                    '1.regex'         => "The MAC Address not valid",
                    '1.required'      => "MAC Address must not be empty!",
                    "2.min"           => "The length of type must not less than :min",
                    '2.max'           => "The maximum length of last name must not exceed :max",
                    '2.required'      => "Type must not be empty!",
                    '3.required'      => "Location must not be empty!",
                    '4.required'      => "Venue must not be empty!",
                    // '5.required'      => "Zone must not be empty!",

                ];


                foreach ($rowData as $key => $value) {


                    $validator = \Validator::make($value, $rules, $messages);


                    if ($validator->fails()) {

                        $error_data[$key][] = [$validator->errors()->first()];
                        // continue;
                    }

                    //checking device name
                    if (OmayaDeviceController::where('name', $value[0])->first()) {
                        
                        $error_data[$key][] = ["The Name [ {$value[0]} ] has already been taken."];
                        // continue;
                        
                    }
                    
                    //checking device mac address
                    $allow_type = config('custom.access_point_type');
                    if (OmayaDeviceController::where('mac_address_separator', $value[1])->first()) {

                        $error_data[$key][] = ["The MAC address [ {$value[1]} ] has already been taken."];
                        // continue;

                    }

                    //checking device type
                    if (!array_key_exists($value[2], $allow_type)) {

                        $error_data[$key][] = ['The selected type does not exist.'];
                        // continue;
                        
                    }
                    unset($allow_type);

                    //checking device location
                    if(!$location = OmayaLocation::where('name', $value[3])->first()){

                        $error_data[$key][] = ["Location [ {$value[3]} ] does not exist"];
                        // continue;

                    }
                    else{
                        $value[3] = $location->location_uid;  
                    }


                    //checking device venue
                    if(!$venue = OmayaVenue::where('name', $value[4])->first()){

                        $error_data[$key][] = ["Venue [ {$value[4]} ] does not exist"];
                        // continue;

                    }
                    else{
                        $value[4] = $venue->venue_uid;  
                    }

                    if(!empty($value[5])) {

                        //checking device zone
                        if(!$zone = OmayaZone::where('name', $value[5])->first()){

                            $error_data[$key][] = ["Zone [ {$value[5]} ] does not exist"];
                            // continue;

                        }
                        else{
                            $value[5] = $zone->zone_uid;  
                        }
                    }


                    if(!isset($error_data[$key])) {



                        while (true) {


                            $uid = randomStringId(8);

                            if (!OmayaDeviceController::where('device_uid', $uid)->first()) {

                                break;
                            }
                        }



                        OmayaDeviceController::create([

                            'tenant_id'             => session('tenant_id'),
                            'device_uid'            => $uid,
                            'name'                  => $value[0],
                            'mac_address'           => str_replace([':', '-'], '', $value[1]),
                            'mac_address_separator' => $value[1],
                            'device_type'           => $value[2],
                            'location_uid'          => $value[3],
                            'venue_uid'             => $value[4],
                            'zone_uid'              => $value[5] ?? NULL,
                            'rssi_min'              => $venue->rssi_min,
                            'rssi_max'              => $venue->rssi_max,
                            'rssi_min_ble'          => $venue->rssi_min_ble,
                            'rssi_max_ble'          => $venue->rssi_max_ble,
                            'dwell_time'            => $venue->dwell_time,
                            'is_default_setting'    => true,
                            'created_by'            => $user->id,
                            'updated_by'            => $user->id,
    
                        ]);

                        $success_data++;
                    }
                    

                }


                unset($user);
                unset($uid);


                fclose($file_data);
            }

            return redirect()->back()->with(["import-error" => $error_data,"data-success" => $success_data]);


        } catch (ValidationException $e) {

            return redirect(route('admin.management.tag.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }
    }
}
