<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaGroup;
use Illuminate\Http\Request;

class ManageGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $groups = OmayaGroup::get();

        return view('v1.admin.manages.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('v1.admin.manages.groups.create');
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
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];

            if(OmayaGroup::where('name', $request->input('name'))->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name already been used.']);

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

            } while (OmayaGroup::where('group_uid', $uid)->first());

            $omy_group = OmayaGroup::create([
                'tenant_id'         => session('tenant_id'),
                'group_uid'         => $uid,
                'name'              => $request->input('name'),
                'remark'            => $request->input('remark'),
                'created_by'        => $omy_user,
                'updated_by'        => $omy_user,
            ]);

            unset($user, $uid, $omy_data);


        } catch (ValidationException $e) {
            return redirect(route('admin.manage.group.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.group.edit', [$omy_group->group_uid]))
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
        if(!$group = OmayaGroup::where('group_uid', $id)->first()) {

            return redirect(route('admin.manage.group.index'))->withErrors(trans('alert.record-not-found'));

        }

        return view('v1.admin.manages.groups.edit', compact('group'));
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
        if(!$group = OmayaGroup::where('group_uid', $id)->first()) {

            return redirect(route('admin.manage.group.index'))->withErrors(trans('alert.record-not-found'));

        }

        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'name'          => 'required|min:3|max:50',
            ];

            $messages = [];

            $this->validate($request, $rules, $messages);
            unset($rules);

            $custom_errors = [];

            if(OmayaGroup::where('name', $request->input('name'))->where('group_uid', '!=', $id)->first()){

                $custom_errors = array_merge($custom_errors, ['name' => 'Name already been used.']);

            }
         
            if($custom_errors) {

                return back()->withErrors($custom_errors)->withInput();
            }
            ############
            ## END VALIDATION PART
            ############


            $omy_user = \Auth::user()->id;



            $group->update([
                'name'              => $request->input('name'),
                'remark'            => $request->input('remark'),
                'updated_by'        => $omy_user,
            ]);

            unset($user, $uid, $omy_data);


        } catch (ValidationException $e) {
            return redirect(route('admin.manage.group.edit', [$group->group_uid]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.group.edit', [$group->group_uid]))
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


            if(!$omy_group = OmayaGroup::where('group_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            // START CHECKING DATA USE IN OTHER TABLE
            $omy_module = [];
 
            if(!empty($omy_module))
            return \Response::json(['status' => 'fail', 'message' => trans('alert.error-delete-data-use', ["module" => implode(", " , $omy_module)])]);

            // END CHECKING DATA USE IN OTHER TABLE

            
            $omy_group->delete();

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }
}
