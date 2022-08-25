<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use DataTables;
use Illuminate\Http\Request;


class ManageZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // // FOR DATATABLE
            // $data = OmayaZone::select('name', 'location_uid', 'venue_uid' , 'updated_at', 'zone_uid')->with(['location']);
            // return Datatables::of($data)
            //     ->addIndexColumn()
            //     ->addColumn('location', function (OmayaZone $omy_zone) {
            //         return $omy_zone->location->map(function($location) {
            //             return $location->name;
            //         });
            //     })
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";

            //         $action_btn .= able_to("manage", "venue", "rw") ? editCustomButton(route('admin.manage.venue.edit', [$data->venue_uid])) : "";
            //         $action_btn .= able_to("manage", "venue", "rw") ? deleteCustomButton(route('admin.manage.venue.destroy', [$data->venue_uid])) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('responsive_id', function($data){
            //         return "";
            //     })
            //     ->rawColumns(['action', 'responsive_id'])
            //     ->make(true);

        }else {

            $zones = OmayaZone::with('location', 'venue')->get();

            return view('v1.admin.manages.zones.index', compact('zones'));

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
        return view('v1.admin.manages.zones.create', compact('locations'));
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
                'name'      => 'required|min:3|max:200',
                'location'  => 'required|exists:omaya_locations,location_uid',
                'venue'     => 'required|exists:omaya_venues,venue_uid',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }

            if(!OmayaVenue::where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->first()){

                $custom_errors = array_merge($custom_errors, ['venue' => 'Venue selected is not valid']);

            }


            if(OmayaZone::where('name', $request->input("name"))->where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has been taken']);
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

            } while (OmayaZone::where('zone_uid', $uid)->first());




            $omy_zone = OmayaZone::create([
                'tenant_id'     => session('tenant_id'),
                'zone_uid'      => $uid,
                'location_uid'  => $request->input('location'),
                'venue_uid'     => $request->input('venue'),
                'name'          => $request->input('name'),
                'remark'        => $request->input('remark'),
                'created_by'    => $omy_user,
                'updated_by'    => $omy_user,
            ]);



        }
        catch (ValidationException $e) {
            return redirect(route('admin.manage.zone.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.zone.edit', $omy_zone->zone_uid))
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
        if(!$zone = OmayaZone::where('zone_uid', $id)->first()) {

            return redirect(route('admin.manage.zone.index'))->withErrors(trans('alert.record-not-found'));

        }

        $locations = OmayaLocation::select('name', 'location_uid')->orderBy('name')->get();

        return view('v1.admin.manages.zones.edit', compact('zone', 'locations'));
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


        if(!$zone = OmayaZone::where('zone_uid', $id)->first()) {

            return redirect(route('admin.manage.zone.index'))->withErrors(trans('alert.record-not-found'));

        }

        try{

            ###########
            ## START VALIDATION PART
            ###########
            $omy_rules = [
                'name'      => 'required|min:3|max:200',
                'location'  => 'required|exists:omaya_locations,location_uid',
                'venue'     => 'required|exists:omaya_venues,venue_uid',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            if(!OmayaLocation::where('location_uid', $request->input('location'))->first()){

                $custom_errors = array_merge($custom_errors, ['location' => 'Location selected is not valid']);

            }

            if(!OmayaVenue::where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->first()){

                $custom_errors = array_merge($custom_errors, ['venue' => 'Venue selected is not valid']);

            }


            if(OmayaZone::where('name', $request->input("name"))->where('location_uid', $request->input('location'))->where('venue_uid', $request->input('venue'))->where('zone_uid', '!=', $id)->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has been taken']);
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

            } while (OmayaZone::where('zone_uid', $uid)->first());




            $zone->update([
                'location_uid'  => $request->input('location'),
                'venue_uid'     => $request->input('venue'),
                'name'          => $request->input('name'),
                'remark'        => $request->input('remark'),
                'updated_by'    => $omy_user,
            ]);



        }
        catch (ValidationException $e) {
            return redirect(route('admin.manage.zone.edit', $zone->zone_uid))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.zone.edit', $zone->zone_uid))
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


            if(!$omy_zone = OmayaZone::where('zone_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            // START CHECKING DATA USE IN OTHER TABLE
            $omy_module = [];
 
            if(OmayaDeviceController::where('zone_uid', $id)->first()) $omy_module[] = "Device ['AP']";


            if(!empty($omy_module))
            return \Response::json(['status' => 'fail', 'message' => trans('alert.error-delete-data-use', ["module" => implode(", " , $omy_module)])]);

            // END CHECKING DATA USE IN OTHER TABLE

            
            $omy_zone->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }


    public function listVenue(Request $request)
    {

        return OmayaVenue::select('name', 'venue_uid')->where('location_uid', $request->input('location_uid'))->orderBy('name')->get();
    }


    public function listZone(Request $request)
    {

        return OmayaZone::select('name', 'zone_uid')->where('venue_uid', $request->input('venue_uid'))->orderBy('name')->get();
    }
}
