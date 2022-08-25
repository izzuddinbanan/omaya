<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaGroup;
use App\Models\OmayaLocation;
use App\Models\OmayaRole;
use App\Models\OmayaRule;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class ManageRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = OmayaRule::get();

        return view('v1.admin.manages.rules.index', compact('rules'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        $data['admin_group']        =  OmayaRole::selectRaw('Distinct(name) as admin_group')->get();

        return view('v1.admin.manages.rules.create', compact('data'));
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
                'type'          => 'required',
                'identifier'    => 'required',
                'event'         => 'required',
                'start_time'    => 'required|date_format:H:i',
                'end_time'      => 'required|date_format:H:i',
                'send'          => 'required|array|min:1',
                'send_to'       => 'required|array|min:1',
                'action_every'  => 'required|integer|min:30'
            ];


            

            $this->validate($request, $omy_rules);
            unset($omy_rules);

            $custom_errors = [];

            if(in_array($request->input('type'), ["device", "device_group"])){

                if($request->input('location') == NULL) {
                    
                    $custom_errors = array_merge($custom_errors, ['location' => 'Location is required if selected Type is [ Device, Device Group] ']);
                }

       
            }else {

                if($request->input('comparison') == NULL || $request->input('value') == NULL) {
                    
                    $custom_errors = array_merge($custom_errors, ['comparison' => 'comparision is required if selected Type is [ Location ] ']);
                    $custom_errors = array_merge($custom_errors, ['value' => 'Value is required if selected Type is [ Location ]] ']);
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

            } while (OmayaRule::where('rule_uid', $uid)->first());




            $omy_rule = OmayaRule::create([
                'tenant_id'     => session('tenant_id'),
                'rule_uid'      => $uid,
                'name'          => $request->input('name'),
                'type'          => $request->input('type'),
                'identifier'    => $request->input('identifier'),
                'event'         => $request->input('event'),
                'comparison'    => $request->input('comparison'),
                'value'         => $request->input('value'),
                'location_uid'  => $request->input('location'),
                'venue_uid'     => $request->input('venue'),
                'zone_uid'      => $request->input('zone'),
                'priority'      => 1,
                'action'        => implode(",", $request->input('send')),
                'action_every'  => $request->input('action_every'),
                'send_to_role'  => implode(",", $request->input('send_to')),
                'start_time_action'  => $this->timeToUtc($request->input('start_time')),
                'stop_time_action'   => $this->timeToUtc($request->input('end_time')),
                'is_active'     => $request->input('is_active') ? true : false,
                'created_by'    => $omy_user,
                'updated_by'    => $omy_user,
            ]);


        }
        catch (ValidationException $e) {
            return redirect(route('admin.manage.rule.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.rule.edit', $omy_rule->rule_uid))
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

        if(!$rule = OmayaRule::where('rule_uid', $id)->first()) {

            return redirect(route('admin.manage.rule.index'))->withErrors(trans('alert.record-not-found'));

        }

        $rule->start_time_action = $this->timeToLocal($rule->start_time_action);
        $rule->stop_time_action  = $this->timeToLocal($rule->stop_time_action);

        $rule->send_to_role = explode(",", $rule->send_to_role);
        $rule->action       = explode(",", $rule->action);

        $data['admin_group']        =  OmayaRole::selectRaw('Distinct(name) as admin_group')->get();

        return view('v1.admin.manages.rules.edit', compact('data', 'rule'));
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


        if(!$omy_rule = OmayaRule::where('rule_uid', $id)->first()) {

            return redirect(route('admin.manage.rule.index'))->withErrors(trans('alert.record-not-found'));

        }


        if($omy_rule->is_default) {

            try{

                ###########
                ## START VALIDATION PART
                ###########
                $omy_rules = [
                    'start_time'    => 'required|date_format:H:i',
                    'end_time'      => 'required|date_format:H:i',
                    'send'          => 'required|array|min:1',
                    'send_to'       => 'required|array|min:1',
                    'action_every'  => 'required|integer|min:30'
                ];

                if($omy_rule->name == "Battery Level" && $request->input('value') == NULL) {
                    $omy_rules['value'] = 'required|integer';
                }


                $this->validate($request, $omy_rules);
                unset($omy_rules);

                $custom_errors = [];

                if($custom_errors) {

                    return back()->withErrors($custom_errors)->withInput();
                }
                ############
                ## END VALIDATION PART
                ############


                $omy_user = \Auth::user()->id;


                $omy_rule->update([
                    'value'         => $request->input('value'),
                    'action'        => implode(",", $request->input('send')),
                    'action_every'  => $request->input('action_every'),
                    'send_to_role'  => implode(",", $request->input('send_to')),
                    'start_time_action'  => $this->timeToUtc($request->input('start_time')),
                    'stop_time_action'   => $this->timeToUtc($request->input('end_time')),
                    'is_active'     => $request->input('is_active') ? true : false,
                    'updated_by'    => $omy_user,
                ]);


            }
            catch (ValidationException $e) {
                return redirect(route('admin.manage.rule.edit', $id))
                    ->withErrors($e->getErrors())
                    ->withInput();
            }

            return redirect(route('admin.manage.rule.edit', $id))
                ->withSuccess(trans('alert.success-update'));




        }else {


            try{

                ###########
                ## START VALIDATION PART
                ###########
                $omy_rules = [
                    'name'          => 'required|min:3|max:200',
                    'type'          => 'required',
                    'identifier'    => 'required',
                    'event'         => 'required',
                    'start_time'    => 'required|date_format:H:i',
                    'end_time'      => 'required|date_format:H:i',
                    'send'          => 'required|array|min:1',
                    'send_to'       => 'required|array|min:1',
                    'action_every'  => 'required|integer|min:10'
                ];


                $this->validate($request, $omy_rules);
                unset($omy_rules);

                $custom_errors = [];

                if(in_array($request->input('type'), ["device", "device_group"])){

                    if($request->input('location') == NULL) {
                        
                        $custom_errors = array_merge($custom_errors, ['location' => 'Location is required if selected Type is [ Device, Device Group] ']);
                    }

           
                }else {

                    if($request->input('comparison') == NULL || $request->input('value') == NULL) {
                        
                        $custom_errors = array_merge($custom_errors, ['comparison' => 'comparision is required if selected Type is [ Location ] ']);
                        $custom_errors = array_merge($custom_errors, ['value' => 'Value is required if selected Type is [ Location ]] ']);
                    }

                }



                if($custom_errors) {

                    return back()->withErrors($custom_errors)->withInput();
                }
                ############
                ## END VALIDATION PART
                ############


                $omy_user = \Auth::user()->id;


                $omy_rule->update([
                    'name'          => $request->input('name'),
                    'type'          => $request->input('type'),
                    'identifier'    => $request->input('identifier'),
                    'event'         => $request->input('event'),
                    'comparison'    => $request->input('comparison'),
                    'value'         => $request->input('value'),
                    'location_uid'  => $request->input('location'),
                    'venue_uid'     => $request->input('venue'),
                    'zone_uid'      => $request->input('zone'),
                    'action'        => implode(",", $request->input('send')),
                    'action_every'  => $request->input('action_every'),
                    'send_to_role'  => implode(",", $request->input('send_to')),
                    'start_time_action'  => $this->timeToUtc($request->input('start_time')),
                    'stop_time_action'   => $this->timeToUtc($request->input('end_time')),
                    'is_active'     => $request->input('is_active') ? true : false,
                    'updated_by'    => $omy_user,
                ]);


            }
            catch (ValidationException $e) {
                return redirect(route('admin.manage.rule.edit', $id))
                    ->withErrors($e->getErrors())
                    ->withInput();
            }

            return redirect(route('admin.manage.rule.edit', $id))
                ->withSuccess(trans('alert.success-update'));
        }
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


            if(!$omy_rule = OmayaRule::where('rule_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }


            if($omy_rule->is_default)
                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);

            
            $omy_rule->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }


    public function getType(Request $request)
    {
        if ($request->ajax()) {


            $data = [];

            if($request->input('type') == "location") {

                $locations = OmayaLocation::orderBy('name')->get();

                foreach ($locations as $location) {
                    $data[] = ["uid" => $location->location_uid, "name" => $location->name];
                }


            }else if($request->input('type') == "venue"){

                $locations = OmayaLocation::orderBy('name')->get();

                foreach ($locations as $location) {

                    $venues = OmayaVenue::orderBy('name')->where('location_uid', $location->location_uid)->get();

                    foreach ($venues as $venue) {

                        $data[] = ["uid" => $venue->venue_uid, "name" => "{$location->name} -> {$venue->name}"];
                    }

                }

            }else if($request->input('type') == "zone"){

                $locations = OmayaLocation::orderBy('name')->get();

                foreach ($locations as $location) {

                    $venues = OmayaVenue::orderBy('name')->where('location_uid', $location->location_uid)->get();

                    foreach ($venues as $venue) {


                        $zones = OmayaZone::orderBy('name')->where('location_uid', $location->location_uid)->where('venue_uid', $venue->venue_uid)->get();

                        foreach ($zones as $zone) {

                            $data[] = ["uid" => $zone->zone_uid, "name" => "{$location->name} -> {$venue->name} -> {$zone->name}"];
                        }
                    }

                }

            }else if($request->input('type') == "device"){

                $devices = OmayaDeviceTracker::orderBy('name')->get();

                foreach ($devices as $device) {

                    $data[] = ["uid" => $device->device_uid, "name" => "{$device->name} [ {$device->mac_address} ]"];
                }

            }else {


                $groups = OmayaGroup::orderBy('name')->get();

                foreach ($groups as $group) {

                    $data[] = ["uid" => $group->group_uid, "name" => $group->name];
                }


            }


            $events = [];
            $locations = [];

            if(in_array($request->input('type'), ['location', 'venue', 'zone'])) {

                $events = config('custom.rules.event_location');


            }else {

                $events = config('custom.rules.event_device');
                $locations = OmayaLocation::orderBy('name')->get();

            }




            return respondAjax("success", "", ['identifier' => $data, 'events' => $events, "locations" => $locations]);


        }

        return abort(403);

    }


    public function getVenue(Request $request)
    {
        return OmayaVenue::select('name', 'venue_uid')->where('location_uid', $request->input('location_uid'))->orderBy('name')->get();
    }

    public function getZone(Request $request)
    {
        return OmayaZone::select('name', 'zone_uid')->where('location_uid', $request->input('location_uid'))->where('venue_uid', $request->input('venue_uid'))->orderBy('name')->get();
    }



    public function timeToUtc($time) {

        $x = $time;
        $x = date('Y-m-d H:i:s', strtotime($x));

        $x = new \DateTime($x, new \DateTimeZone(session('timezone')));
        $x->setTimezone(new \DateTimeZone("UTC"));

        return $x->format('H:i');

    }


    public function timeToLocal($time) {

        $x = $time;
        $x = date('Y-m-d H:i:s', strtotime($x));

        $x = new \DateTime($x, new \DateTimeZone("UTC"));
        $x->setTimezone(new \DateTimeZone(session('timezone')));

        return $x->format('H:i');

    }


}
