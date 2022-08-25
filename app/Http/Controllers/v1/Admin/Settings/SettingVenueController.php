<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OmayaVenue;
use App\Models\OmayaWebuser;
use App\Models\Cloud;
use App\Models\OmayaVenueMap;
use App\Models\VenueDev;
use DataTables;


class SettingVenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('v1.admin.settings.venue.index');
    }

    public function dataTable(Request $request)
    {

        if ($request->ajax()) {

            $raw_count = 1;

            $data = OmayaVenue::orderByDesc('updated_at');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $action_btn  = "";
                    $action_btn .= able_to("setting", "venue", "r") ? editCustomButton(route('admin.setting.venue.edit', [$data->venue_uid])) : "";
                    $action_btn .= able_to("setting", "venue", "rw") ? deleteCustomButton(route('admin.setting.venue.destroy', [$data->venue_uid])) : "";
        
                    return actionCustomButton($action_btn);

                })
                ->addColumn('responsive_id', function($data){
                    $responsive_id = "";
                    return $responsive_id;

                })
                ->addColumn('image', function($data){
                    $map = '<img src="'.$data->getThumbnailImageUrl(session('tenant_id')).'" class="img img-fluid" style="height:100px !important;">';
                    return $map;

                })
                ->addColumn('heatmap_stat', function($data){
                   
                    $total_device = $data->device()->count();

                    if($total_device >= 3){
                        // $heatmap = '<a href="#" id="editable" data-type="text" data-pk="1" data-name="username" data-url="post.php" data-original-title="Enter username">';
                        
                        // if($data->heatmap_stat == 0) $heatmap .= 'Presence';
                        // else  $heatmap .= 'Historical';

                        // $heatmap .= '</a>';

                        $heatmap = '<select class="form-control heatmap" name="heatmap_stat" data-uid="'.$data->venue_uid.'">
                                        <option value="1" '.($data->heatmap_stat == 1? 'selected': '').'>Historical</option>
                                        <option value="0" '.($data->heatmap_stat == 0? 'selected': '').'>Presence</option>
                                    </select>';


                    }
                    else $heatmap = 'Presence';

                    return $heatmap;

                })
                ->rawColumns(['action', 'raw_count', 'responsive_id', 'image','heatmap_stat'])
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

        $data['company'] = Cloud::where('is_active', 1)->where('tenant_id', session('tenant_id'))->get();
        return view('v1.admin.settings.venue.create', compact('data'));

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

            // dd($request->all());

            //START VALIDATION
            $rules = [
                'venue_name'    => 'required|min:4|max:50',
                'venue_type'    => 'required',
                // 'company_id'    => 'required',
                'venue_address' => 'required_if:venue_type,outlet',
                'dev_type'      => 'required',
                'rssi_min'      => 'required|integer|max:-1|min:-100',
                'rssi_enter'    => 'required|integer|max:-1|min:-100',
                'dwell'         => 'required|integer|max:600|min:0',
                'left_time'     => 'required|integer|max:600|min:0',
                'force_exit'    => 'required',
                'time'          => 'required_if:force_exit,time',
                'no_of_second'  => 'required_if:force_exit,no_of_second',
                'scale_m'       => 'required',
                'map'           => 'required|image|mimes:jpg,png,jpeg|max:5000',
            ];

            if($request->venue_type != 'outlet'){
                $rules['venue_level'] = 'required';
                $rules['venue_zone'] = 'required';
            }

            $this->validate($request, $rules);
            unset($rules);

            if(OmayaVenue::where('venue_name', $request->company_id)->first()){
                return back()->withErrors(['venue_name' => 'Name selected is not valid'])->withInput();
            }

            
            //END VALIDATION

            $user = \Auth::user();
            
            while (true){
                
                $uid = randomStringId(8);
                
                if(!OmayaVenue::where('venue_uid', $uid)->first()){
                    
                    break;
                }
            }

            if($request->hasFile('map')){

                $omy_temp = getimagesize($request->file('map'));
                $omy_data['image_width']    = $omy_temp[0];
                $omy_data['image_height']   = $omy_temp[1];

                unset($omy_temp);

                $path_image = 'app/public/tenants/' . session('tenant_id') . '/venues/';

            }


            $venue = OmayaVenue::create([
                'tenant_id'     => session('tenant_id'),
                'venue_uid'     => $uid,
                'venue_name'    => $request->venue_name,
                'venue_type'    => $request->venue_type,
                'venue_address' => $request->venue_address,
                'venue_level'   => $request->venue_level,
                'venue_zone'    => $request->venue_zone,
                'dev_type'      => $request->dev_type,
                'rssi_min'      => $request->rssi_min,
                'rssi_max'      => $request->rssi_enter,
                'dwell'         => $request->dwell,
                'left_time'     => $request->left_time,
                'force_exit'    => $request->force_exit,
                'time'          => $request->time,
                'no_of_second'  => $request->no_of_second,
                'scale_m'       => $request->scale_m,
                'image'         => $request->hasFile('map') ? \App\Processors\SaveImageProcessor::make($request->file('map'), $path_image)->execute() : NULL,
                'image_width'   => $omy_data['image_width'],
                'image_height'  => $omy_data['image_height'],
                'created_by'    => $user->id,
                'updated_by'    => $user->id,
            ]);

            unset($user);
            // unset($uid);



        }catch (ValidationException $e) {
            return redirect(route('admin.setting.venue.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.venue.edit', [$uid]))
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
    public function edit($uid)
    {
        $company    = Cloud::where('is_active', 1)->where('tenant_id', session('tenant_id'))->get();
        $venue      = OmayaVenue::where('venue_uid', $uid)->first();  

        return view('v1.admin.settings.venue.edit', compact('company', 'venue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {

        if(!$venue = OmayaVenue::where('venue_uid', $uid)->first()){

            return redirect(route('admin.setting.venue.index'))
                ->withErrors(trans('alert.record-not-found'));

        }

        try{

            //START VALIDATION
            $rules = [
                'venue_name'    => 'required|min:4|max:50',
                'venue_type'    => 'required',
                // 'company_id'    => 'required',
                'venue_address' => 'required_if:venue_type,outlet',
                'dev_type'      => 'required',
                'rssi_min'      => 'required|integer|max:-1|min:-100',
                'rssi_enter'    => 'required|integer|max:-1|min:-100',
                'dwell'         => 'required|integer|max:600|min:0',
                'left_time'     => 'required|integer|max:600|min:0',
                'force_exit'    => 'required',
                'time'          => 'required_if:force_exit,time',
                'no_of_second'  => 'required_if:force_exit,no_of_second',
                'scale_m'       => 'required',
                'map'           => 'required|image|mimes:jpg,png,jpeg|max:5000',
            ];

            if($request->venue_type != 'outlet'){
                $rules['venue_level'] = 'required';
                $rules['venue_zone'] = 'required';
            }

            $this->validate($request, $rules);
            unset($rules);

            if(OmayaVenue::where('venue_name', $request->company_id)->first()){
                return back()->withErrors(['venue_name' => 'Name selected is not valid'])->withInput();
            }

            
            //END VALIDATION

            $user = \Auth::user();
            
            $venue->update([
                'venue_name'    => $request->venue_name,
                'venue_type'    => $request->venue_type,
                'venue_address' => $request->venue_type == 'outlet' ? $request->venue_address : NULL,
                'venue_level'   => $request->venue_type != 'outlet' ? $request->venue_level : NULL,
                'venue_zone'    => $request->venue_type != 'outlet' ? $request->venue_zone : NULL,
                'dev_type'      => $request->dev_type,
                'rssi_min'      => $request->rssi_min,
                'rssi_max'      => $request->rssi_enter,
                'dwell'         => $request->dwell,
                'left_time'     => $request->left_time,
                'force_exit'    => $request->force_exit,
                'time'          => $request->force_exit == 'time' ? $request->time : NULL,
                'no_of_second'  => $request->force_exit == 'no_of_second' ? $request->no_of_second : NULL,
                'updated_by'    => $user->id,
            ]);

            unset($user);

            if($request->hasFile('map')){

                $omy_temp = getimagesize($request->file('map'));
                $omy_data['image_width']    = $omy_temp[0];
                $omy_data['image_height']   = $omy_temp[1];

                unset($omy_temp);

                $path_image = 'app/public/tenants/' . session('tenant_id') . '/venues/';


                ### DELETE IMAGE ORIGINAL AND THUMNAIL 
                if ($venue->image != null) $this->deleteImage($venue);


                ## STORE IMAGE
                $venue->image           = \App\Processors\SaveImageProcessor::make($request->file('map'), $path_image)->execute();
                $venue->image_width     = $omy_data['image_width'];
                $venue->image_height    = $omy_data['image_height'];
                $venue->save();

            }



        }catch (ValidationException $e) {
            return redirect(route('admin.setting.venue.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.venue.edit', [$uid]))
            ->withSuccess(trans('alert.success-update'));
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

            $check = OmayaVenue::where('venue_uid', $uid);
            if(count($check->get()) == 0){
                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $check->delete();
            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }

    public function deleteImage($venue)
    {
        \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/' .$venue->image));
        \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/venues/thumbnails/' . removeStringAfterCharacters($venue->image) .'.jpg'));
    }

    public function ajaxVenueHeatmap(Request $request){

        if($request->ajax()){

            $heatmap_stat   = $request->heatmap;
            $venue_uid      = $request->venue_uid;

            $venue = OmayaVenue::where('venue_uid', $venue_uid)->update(['heatmap_stat' => $heatmap_stat]);

            if(!$venue) return false;

            return true;
        }
        else return false;
        


    }

}
