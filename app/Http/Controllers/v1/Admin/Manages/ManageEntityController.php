<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaEntity;
use App\Models\OmayaGroup;
use Illuminate\Http\Request;

class ManageEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = OmayaEntity::get();

        return view('v1.admin.manages.entities.index', compact('entities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $groups  = OmayaGroup::get();
        $devices = OmayaDeviceTracker::where('is_allocated', false)->where('is_active', true)->get();
        $users   = OmayaEntity::where('type', 'staff')->get();

        return view('v1.admin.manages.entities.create', compact('groups', 'devices', 'users'));
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
                'name'          => 'required|min:3|max:50',
                'type'          => 'required',
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];


            if(OmayaEntity::where('name', $request->input('name'))->where('type', $request->input('type'))->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has already been use']);

            }

            if($request->input('type') == "visitor" && !empty($request->input('meet_with'))) {

                if(!OmayaEntity::where('entity_uid', $request->input('meet_with'))->where('type', 'staff')->first()){

                    $custom_errors = array_merge($custom_errors, ['meet_with' => 'Selected meet with not found in record.']);

                }

            }


            if(!empty($request->input('device'))) {

                if(!OmayaDeviceTracker::where('device_uid', $request->input('device'))->where('is_allocated', false)->where('is_active', true)->first()){

                    $custom_errors = array_merge($custom_errors, ['device' => 'Selected device [tracker]  not found in record.']);

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

            } while (OmayaEntity::where('entity_uid', $uid)->first());



            $omy_entity = OmayaEntity::create([
                'tenant_id'         => session('tenant_id'),
                'entity_uid'        => $uid,
                'device_tracker_uid'=> $request->input('device'),
                'meet_entity_uid'   => ($request->input('type') == "visitor" ? $request->input('meet_with') : NULL),
                'group_uid'         => $request->input('group'),
                'name'              => $request->input('name'),
                'type'              => $request->input('type'),
                'remarks'           => $request->input('remark'),
                'created_by'        => $omy_user,
                'updated_by'        => $omy_user,
            ]);


            OmayaDeviceTracker::where('device_uid', $request->input('device'))->where('is_allocated', false)->update(['is_allocated' => true]);


            unset($user, $uid, $omy_data);




        } catch (ValidationException $e) {
            return redirect(route('admin.manage.entity.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.entity.edit', [$omy_entity->entity_uid]))
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
        if(!$entity = OmayaEntity::where('entity_uid', $id)->first()) {

            return redirect(route('admin.manage.entity.index'))->withErrors(trans('alert.record-not-found'));

        }

        $groups  = OmayaGroup::get();
        $devices = OmayaDeviceTracker::where('is_allocated', false)->orWhere('device_uid', $entity->device_tracker_uid)->where('is_active', true)->get();
        $users   = OmayaEntity::where('type', 'staff')->get();

        return view('v1.admin.manages.entities.edit', compact('groups', 'devices', 'users', 'entity'));
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

        if(!$entity = OmayaEntity::where('entity_uid', $id)->first()) {

            return redirect(route('admin.manage.entity.index'))->withErrors(trans('alert.record-not-found'));

        }

        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'name'          => 'required|min:3|max:50',
                'type'          => 'required',
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];


            if(OmayaEntity::where('name', $request->input('name'))->where('entity_uid', '!=', $id)->where('type', $request->input('type'))->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name has already been use']);

            }

            if($request->input('type') == "visitor" && !empty($request->input('meet_with'))) {

                if(!OmayaEntity::where('entity_uid', $request->input('meet_with'))->where('type', 'staff')->first()){

                    $custom_errors = array_merge($custom_errors, ['meet_with' => 'Selected meet with not found in record.']);

                }

            }  




            if(!empty($request->input('device'))) {

                if($request->input('device') != $entity->device_tracker_uid) {

                    if(!OmayaDeviceTracker::where('device_uid', $request->input('device'))->where('is_allocated', false)->where('is_active', true)->first()){

                        $custom_errors = array_merge($custom_errors, ['device' => 'Selected device [tracker]  not found in record.']);

                    }
                }

            }
        
            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }
            ############
            ## END VALIDATION PART
            ############


            $omy_user = \Auth::user()->id;


            OmayaDeviceTracker::where('device_uid', $entity->device_tracker_uid)->where('is_allocated', true)->update(['is_allocated' => false]);
            
            $entity->update([
                'tenant_id'         => session('tenant_id'),
                'device_tracker_uid'=> $request->input('device'),
                'meet_entity_uid'   => ($request->input('type') == "visitor" ? $request->input('meet_with') : NULL),
                'group_uid'         => $request->input('group'),
                'name'              => $request->input('name'),
                'type'              => $request->input('type'),
                'remarks'           => $request->input('remark'),
                'updated_by'        => $omy_user,
            ]);



            OmayaDeviceTracker::where('device_uid', $request->input('device'))->where('is_allocated', false)->update(['is_allocated' => true]);


            unset($user, $uid, $omy_data);



        } catch (ValidationException $e) {
            return redirect(route('admin.manage.entity.edit', [$id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.entity.edit', [$id]))
            ->withSuccess(trans('alert.success-create'));
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


            if(!$omy_entity = OmayaEntity::where('entity_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            if($omy_entity->device_tracker_uid) {

                OmayaDeviceTracker::where('device_uid', $omy_entity->device_tracker_uid)->where('is_allocated', true)->update(['is_allocated' => false]);
                
            }
          
            $omy_entity->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }

}
