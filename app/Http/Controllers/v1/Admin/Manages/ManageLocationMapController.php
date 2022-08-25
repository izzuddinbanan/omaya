<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaCloud;
use App\Models\OmayaLocation;
use Illuminate\Http\Request;

class ManageLocationMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first();

        $locations = OmayaLocation::get();

        return view('v1.admin.manages.location-maps.index', compact('cloud', 'locations'));

    }

    public function savePosition(Request $request)
    {   

        if ($request->ajax()) {

            $location = OmayaLocation::where('location_uid', $request->input('location_uid'))->first();
            if(!$location){
                return respondAjax("false", "Record not found." , ["locations" => $locations]);
            }

            $location->position_x = $request->input('position')[0];
            $location->position_y = $request->input('position')[1];
            $location->updated_by = \Auth::user()->id;
            $location->save();

            return respondAjax("success", "Successfully update the AP position.");
        }

        return \Response::json(['status' => 'fail']);


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
        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'map'           => 'required|image|mimes:jpg,png,jpeg|max:15000',
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            ############
            ## END VALIDATION PART
            ############


            $omy_user = \Auth::user()->id;



            $omy_temp = getimagesize($request->file('map'));
            $omy_data['image_width']    = $omy_temp[0];
            $omy_data['image_height']   = $omy_temp[1];

            unset($omy_temp);

            $path_image = 'app/public/tenants/' . session('tenant_id') . '/locations/';

            
            $cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first();

            $cloud->update([
                'location_image'             => \App\Processors\SaveImageProcessor::make($request->file('map'), $path_image)->execute(),
                'location_image_width'       => $omy_data['image_width'] ?? null,
                'location_image_height'      => $omy_data['image_height'] ?? null,
                'updated_by'        => $omy_user,
            ]);


        } catch (ValidationException $e) {
            return redirect(route('admin.manage.location-map.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.location-map.index'))
            ->withSuccess(trans('alert.success-update'));
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
