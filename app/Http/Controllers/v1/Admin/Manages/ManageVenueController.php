<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use DataTables;
use Illuminate\Http\Request;

class ManageVenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // FOR DATATABLE
            // $data = OmayaVenue::with('location')->select(['omaya_venues.*']);
            // return Datatables::eloquent($data)
            //     ->addIndexColumn()
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";

            //         $action_btn .= able_to("manage", "venue", "rw") ? editCustomButton(route('admin.manage.venue.edit', [$data->venue_uid])) : "";
            //         $action_btn .= able_to("manage", "venue", "rw") ? deleteCustomButton(route('admin.manage.venue.destroy', [$data->venue_uid])) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('responsive_id', function($data){
            //         return "";
            //     })
            //     ->addColumn('image', function($data){

            //         return '<a href="#" class="modal-view-image" data-src="'. $data->getThumbnailImageUrlAttribute() .'"><img src="'.$data->getThumbnailImageUrlAttribute().'" class="img img-fluid" style="height:100px !important;">';
            //     })
            //     ->rawColumns(['action', 'responsive_id', 'image'])
            //     ->filterColumn('fullname', function($query, $keyword) {
            //         $sql = "CONCAT(users.first_name,'-',users.last_name)  like ?";
            //         $query->whereRaw($sql, ["%{$keyword}%"]);
            //     })
            //     ->toJson();
            //     ->toJson();

        }else {

            $venues = OmayaVenue::with('location')->get();

            return view('v1.admin.manages.venues.index', compact('venues'));

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = OmayaLocation::select('name', 'location_uid')->orderBy('name')->get();
        return view('v1.admin.manages.venues.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'location'      => 'required|exists:omaya_locations,location_uid',
                'name'          => 'required|min:3|max:50',
                'level'         => 'required|max:10',
                'space_length'  => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
                'map'           => 'nullable|image|mimes:jpg,png,jpeg|max:15000',
                'rssi_min'      => 'required|integer|max:0|min:-128',
                'rssi_max'      => 'required|integer|max:0|min:-128',
                'rssi_min_ble'  => 'required|integer|max:0|min:-128',
                'rssi_max_ble'  => 'required|integer|max:0|min:-128',
                'dwell_time'    => 'required|integer|max:30|min:0',
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];

            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }


            if(OmayaVenue::where('location_uid', $request->input('location'))->where('name', $request->input('name'))->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has already been use']);

            }

            if($request->hasFile('map') && !$request->input('space_length')) {

                $custom_errors = array_merge($custom_errors, ['space_length' => 'Space length is required if map is uploaded.']);

            }


            if($request->hasFile('map')){
                
                //calculate distance within two points
                $points         = explode(',', $request->input('points'));

                if(count($points) != 4){

                    $custom_errors = array_merge($custom_errors, ['map' => 'Please draw map correctly.']);

                }


            }

            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }
            ############
            ## END VALIDATION PART
            ############


            $omy_user = \Auth::user()->id;


            do {

                $uid = randomStringId();

            } while (OmayaVenue::where('venue_uid', $uid)->first());


            if($request->hasFile('map')){

                $omy_temp = getimagesize($request->file('map'));
                $omy_data['image_width']    = $omy_temp[0];
                $omy_data['image_height']   = $omy_temp[1];

                unset($omy_temp);

                $path_image = 'app/public/tenants/' . session('tenant_id') . '/venues/';

                $point_distance = distance($points[0], $points[1], $points[2], $points[3]);

            }


            $omy_venue = OmayaVenue::create([
                'tenant_id'         => session('tenant_id'),
                'location_uid'      => $request->input('location'),
                'venue_uid'         => $uid,
                'name'              => $request->input('name'),
                'level'             => $request->input('level'),
                'space_length_point'=> $request->input('points'),
                'space_length_meter'=> $request->input('space_length'),
                'space_length_px'   => $point_distance ?? null,
                'image'             => $request->hasFile('map') ? \App\Processors\SaveImageProcessor::make($request->file('map'), $path_image)->execute() : NULL,
                'image_width'       => $omy_data['image_width'] ?? null,
                'image_height'      => $omy_data['image_height'] ?? null,
                'rssi_min'          => $request->input('rssi_min'),
                'rssi_max'          => $request->input('rssi_max'),
                'rssi_min_ble'      => $request->input('rssi_min_ble'),
                'rssi_max_ble'      => $request->input('rssi_max_ble'),
                'dwell_time'        => $request->input('dwell_time'),
                'created_by'        => $omy_user,
                'updated_by'        => $omy_user,
            ]);

            unset($user, $uid, $omy_data);




        } catch (ValidationException $e) {
            return redirect(route('admin.manage.venue.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.venue.edit', [$omy_venue->venue_uid]))
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
        
        if(!$venue = OmayaVenue::where('venue_uid', $id)->first()) {

            return redirect(route('admin.manage.venue.index'))->withErrors(trans('alert.record-not-found'));

        }

        $locations = OmayaLocation::select('name', 'location_uid')->orderBy('name')->get();

        return view('v1.admin.manages.venues.edit', compact('venue', 'locations'));
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

        if(!$venue = OmayaVenue::where('venue_uid', $id)->first()) {

            return redirect(route('admin.manage.venue.index'))->withErrors(trans('alert.record-not-found'));

        }

        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'location'      => 'required|exists:omaya_locations,location_uid',
                'name'          => 'required|min:3|max:50',
                'level'         => 'required|max:10',
                'space_length'  => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
                'map'           => 'nullable|image|mimes:jpg,png,jpeg|max:15000',
                'rssi_min'      => 'required|integer|max:0|min:-128',
                'rssi_max'      => 'required|integer|max:0|min:-128',
                'rssi_min_ble'  => 'required|integer|max:0|min:-128',
                'rssi_max_ble'  => 'required|integer|max:0|min:-128',
                'dwell_time'    => 'required|integer|max:30|min:0',
            ];

            $messages = [
            ];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];
            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }


            if(OmayaVenue::where('location_uid', $request->input('location'))->where('name', $request->input('name'))->where('venue_uid', '!=', $id)->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has already been use']);

            }


            if(($request->hasFile('map') && !$request->input('space_length')) || ($venue->image && !$request->input('space_length'))) {

                $custom_errors = array_merge($custom_errors, ['space_length' => 'Space length is required if map is uploaded.']);

            }



            if($request->hasFile('map') || $venue->image){
                
                //calculate distance within two points
                $points         = explode(',', $request->input('points'));

                if(count($points) != 4){

                    $custom_errors = array_merge($custom_errors, ['map' => 'Please draw map correctly.']);

                }else {
                    
                    //calculate distance within two points
                    $point_distance = distance($points[0], $points[1], $points[2], $points[3]);

                }

            }

            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }



            ############
            ## END VALIDATION PART
            ############


            $omy_user = \Auth::user()->id;



            $venue->update([
                'location_uid'      => $request->input('location'),
                'name'              => $request->input('name'),
                'level'             => $request->input('level'),
                'space_length_point'=> $request->input('points'),
                'space_length_meter'=> $request->input('space_length'),
                'space_length_px'   => $point_distance ?? NULL,
                'rssi_min'          => $request->input('rssi_min'),
                'rssi_max'          => $request->input('rssi_max'),
                'rssi_min_ble'      => $request->input('rssi_min_ble'),
                'rssi_max_ble'      => $request->input('rssi_max_ble'),
                'dwell_time'        => $request->input('dwell_time'),
                'updated_by'        => $omy_user,
            ]);

            unset($user, $uid, $omy_data);


            if($request->hasFile('map')){

                $omy_temp = getimagesize($request->file('map'));
                $omy_data['image_width']    = $omy_temp[0];
                $omy_data['image_height']   = $omy_temp[1];

                unset($omy_temp);

                $path_image = 'app/public/tenants/' . session('tenant_id') . '/venues/';

                ### DELETE IMAGE ORIGINAL AND THUMNAIL 
                \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/' .$venue->image));
                \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/thumbnails/' . removeStringAfterCharacters($venue->image) .'.jpg'));

                ## STORE IMAGE
                $venue->image           = \App\Processors\SaveImageProcessor::make($request->file('map'), $path_image)->execute();
                $venue->image_width     = $omy_data['image_width'];
                $venue->image_height    = $omy_data['image_height'];
                $venue->save();

            }


            OmayaDeviceController::where('venue_uid', $venue->venue_uid)->where('is_default_setting', true)->update(
                [
                    'rssi_min'      => $request->input('rssi_min'), 
                    'rssi_max'      => $request->input('rssi_max'), 
                    'rssi_min_ble'  => $request->input('rssi_min_ble'), 
                    'rssi_max_ble'  => $request->input('rssi_max_ble'), 
                    'dwell_time'    => $request->input('dwell_time')
                ]);





        } catch (ValidationException $e) {
            return redirect(route('admin.manage.venue.edit', [$venue->venue_uid]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.venue.edit', [$venue->venue_uid]))
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


            if(!$omy_venue = OmayaVenue::where('venue_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            // START CHECKING DATA USE IN OTHER TABLE
            $omy_module = [];
 
            if(OmayaZone::where('venue_uid', $id)->first()) $omy_module[] = "Zone";

            if(OmayaDeviceController::where('venue_uid', $id)->first()) $omy_module[] = "Device ['AP']";


            if(!empty($omy_module))
            return \Response::json(['status' => 'fail', 'message' => trans('alert.error-delete-data-use', ["module" => implode(", " , $omy_module)])]);

            // END CHECKING DATA USE IN OTHER TABLE


            ### DELETE IMAGE ORIGINAL AND THUMNAIL 
            \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/' .$omy_venue->image));
            \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/thumbnails/' . removeStringAfterCharacters($omy_venue->image) .'.jpg'));

            OmayaZone::where('venue_uid', $id)->delete();
            
            $omy_venue->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }


    public function mapView($id)
    {
        

        if(!$venue = OmayaVenue::where('venue_uid', $id)->first()) {

            return redirect(route('admin.manage.venue.index'))->withErrors(trans('alert.record-not-found'));

        }


        if(!$venue->image) return redirect(route('admin.manage.venue.index'))->withErrors("Please upload map first for this venue [ {$venue->name} ].");


        $devices = OmayaDeviceController::where('venue_uid', $id)->get();

        return view('v1.admin.manages.venues.map', compact('venue', 'devices'));

    }

    public function saveLocationPosition(Request $request){

        $ap = OmayaDeviceController::where('device_uid', $request->input('device_uid'))->first();
        
        if(!$ap){
            return ["status" => false, "data" => [], "message" => "Record not found."];
        }

        $ap->position_x = $request->input('position')[0];
        $ap->position_y = $request->input('position')[1];
        $ap->updated_by = \Auth::user()->id;
        
        $ap->save();

        return ["status" => true, "data" => $request->input(), "message" => "Successfully update the AP position."];

    }
}
