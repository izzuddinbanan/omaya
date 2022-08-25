<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateOuiFilter;
use App\Models\OmayaCloud;
use App\Models\OmayaVenue;
use Illuminate\Http\Request;

class SettingFilteringController extends Controller
{
    public function index(){

        //automatic filtering
        $venues = OmayaVenue::all();
        $tenant = OmayaCloud::where('tenant_id', session('tenant_id'))->first();

        return view('v1.admin.settings.filtering.index', compact('venues', 'tenant'));
    }

    public function ajax_auto_filter_venue(Request $request){

        if($request->ajax()){

            $venue = OmayaVenue::where('venue_uid', $request->venue_uid)->first();

            return $venue;
        }

    }

    public function update_auto_filter_venue(Request  $request){

        try{

            $rules = [
                'venue' => 'required'
            ];

            $this->validate($request, $rules);
            unset($rules);

            if($request->has('filter_status')){
                if($request->venue == "All Venue"){
    
                    foreach(OmayaVenue::get() as $venue){
                        $venue->update([
                            'filter_status'     => 1,
                            'staff_dwell_hour'  => $request->staff_dwell_hour,
                            'staff_day'         => $request->staff_day,
                            'dev_dwell_hour'    => $request->dev_dwell_hour,
                            'dev_day'           => $request->dev_day,
                            'dwell_last_update' => date('Y-m-d H:i:s')
                        ]);
                    }

                }
                else{

                    OmayaVenue::where('venue_uid', $request->venue)->update([
                        'filter_status'     => 1,
                        'staff_dwell_hour'  => $request->staff_dwell_hour,
                        'staff_day'         => $request->staff_day,
                        'dev_dwell_hour'    => $request->dev_dwell_hour,
                        'dev_day'           => $request->dev_day,
                        'dwell_last_update' => date('Y-m-d H:i:s')
                    ]);

                }

            }
            else{

                foreach(OmayaVenue::get() as $venue){
                    $venue->update([
                        'filter_status'     => 0,
                        'staff_dwell_hour'  => 0,
                        'staff_day'         => 0,
                        'dev_dwell_hour'    => 0,
                        'dev_day'           => 0,
                        'dwell_last_update' => date('Y-m-d H:i:s')
                    ]);
                }
            }


        }catch (ValidationException $e) {
            return redirect(route('admin.setting.filtering.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.filtering.index'))
            ->withSuccess(trans('alert.success-update'));

    }

    public function update(Request  $request){

        if(!$tenant = OmayaCloud::where('tenant_id', session('tenant_id'))->first()){
            return redirect(route('admin.setting.filtering.index'))
                ->withErrors(trans('alert.record-not-found'));
        }

        try{

            //START validate input
            $rules = array();

            if(isset($request->is_filter_oui)){
                $rules['is_filter_oui'] = 'required';
            }

            // if(isset($request->mac_random_status)){
            //     $rules['mac_random_status'] = 'required';
            // }

            if(isset($request->is_filter_dwell_time)){
                $rules['is_filter_dwell_time'] = 'required';
            }

            $this->validate($request, $rules);
            unset($rules);

            //END validate input

            if(isset($request->is_filter_oui)){
                $tenant->update(['is_filter_oui' => $request->is_filter_oui]);
            }

            if(isset($request->is_filter_mac_random)){
                $tenant->update(['is_filter_mac_random' => $request->is_filter_mac_random]);
            }

            if(isset($request->is_filter_dwell_time)){
                $tenant->update(['is_filter_dwell_time' => $request->is_filter_dwell_time, 'remove_dwell_time' => $request->remove_dwell_time]);
            }


        }catch (ValidationException $e) {
            return redirect(route('admin.setting.filtering.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.filtering.index'))
            ->withSuccess(trans('alert.success-update'));

    }

    public function ajax_update_oui_list(){

        //call function from helper

        // UpdateOuiFilter::dispatch();
        // if(pullOuiStandard()){

            return ["status" => 'success'];
        // }

        // return ["status" => 'failed'];


    } 
    
    
}
