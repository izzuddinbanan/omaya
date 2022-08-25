<?php

namespace App\Http\Controllers\v1\Admin\Clouds;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OmayaCloud;
use App\Models\Module;
use App\Models\OmayaConfig;
use App\Models\OmayaModule;
use App\Models\OmayaRole;
use App\Models\Role;
use App\Models\OmayaUser;

use DataTables;


class CloudTenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // $raw_count = 1;

            // $data = OmayaCloud::where('is_active' ,1)->orderByDesc('updated_at');
            // return Datatables::of($data)
            //     ->addIndexColumn()
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";
            //         $action_btn .= able_to("cloud", "tenant", "r") ? editCustomButton(route('admin.cloud.tenant.edit', [$data->cloud_uid])) : "";
            //         $action_btn .= able_to("cloud", "tenant", "rw") ? deleteCustomButton(route('admin.cloud.tenant.destroy', [$data->name])) : "";
            //         $action_btn .= able_to("cloud", "tenant", "rw") ? suspendCustomButton(route('admin.cloud.tenant.suspend', [$data->name]),  'Suspend Account', 'ajaxSuspendButton', 'fa fa-ban' ) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('raw_count', function($row) use($raw_count) {
        
            //         return $raw_count++;

            //     })
            //     ->addColumn('responsive_id', function($row) {
            //         $responsive_id = "";
            //         return $responsive_id;

            //     })
            //     ->editColumn('updated_at', function($data) {
            //         return date('Y-m-d H:i:s', strtotime($data->updated_at));
            //     })
            //     ->rawColumns(['action', 'raw_count','responsive_id'])
            //     ->make(true);
        }
        else{
            $clouds = OmayaCloud::get();
            return view('v1.admin.clouds.tenants.index', compact('clouds'));
        }
    }

    public function dataTable(Request $request)
    {

        if ($request->ajax()) {

            $raw_count = 1;

            $data = OmayaCloud::where('is_active' ,1)->orderByDesc('updated_at');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $action_btn  = "";
                    $action_btn .= able_to("cloud", "tenant", "r") ? editCustomButton(route('admin.cloud.tenant.edit', [$data->cloud_uid])) : "";
                    $action_btn .= able_to("cloud", "tenant", "rw") ? deleteCustomButton(route('admin.cloud.tenant.destroy', [$data->name])) : "";
                    $action_btn .= able_to("cloud", "tenant", "rw") ? suspendCustomButton(route('admin.cloud.tenant.suspend', [$data->name]),  'Suspend Account', 'ajaxSuspendButton', 'fa fa-ban' ) : "";
        
                    return actionCustomButton($action_btn);

                })
                ->addColumn('raw_count', function($row) use($raw_count) {
        
                    return $raw_count++;

                })
                ->addColumn('responsive_id', function($row) {
                    $responsive_id = "";
                    return $responsive_id;

                })
                ->editColumn('updated_at', function($data) {
                    return date('Y-m-d H:i:s', strtotime($data->updated_at));
                })
                ->rawColumns(['action', 'raw_count','responsive_id'])
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
        return view('v1.admin.clouds.tenants.create');
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
                'name'                  => 'min:4|max:50|required',
                'tenant_id'             => 'min:4|max:20|required|unique:omaya_clouds,tenant_id|string',
                'admin_id'              => 'min:4|max:20|required',
                'password'              => 'required|min:5|confirmed|',//regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'email'                 => 'nullable|email',
                'license_key'           => 'required|min:25',
            ];


            $this->validate($request, $rules);
            unset($rules);

            if(OmayaCloud::where('tenant_id', $request->input('tenant_id'))->first()){

                return back()->withErrors(['tenant_id' => 'Tenant ID has already been taken.'])->withInput();
            }

            $license = checkLicense($request->input('license_key'));
            if(!$license["result"]){
                
                return back()->withErrors(['license_key' => 'License key is not valid.'])->withInput();
            }

            if($license['client_name'] != $request->input('tenant_id')){

                return back()->withErrors([
                        'license_key' => 'Tenant ID entered not same as license ',
                        'tenant_id' => 'Tenant ID entered not same as license '
                    ])->withInput();

            }
            

            ############
            ## END VALIDATION PART
            ############


            $user = \Auth::user();

            $cloud = OmayaCloud::create([
                'tenant_id'         => $request->input('tenant_id'),
                'name'              => $request->input('name'),
                'is_active'         => true,
                'phone'             => $request->input('phone'),
                'email'             => $request->input('email'),
                'license_key'       => $request->input('license_key'),
                'address'           => $request->input('address'),
                'is_active'         => true,
                'created_by'        => $user->id,
                'expired_at'        => date('Y-m-d H:i:s', $license['expire_on']),
                'updated_by'        => $user->id,
                'deleted_at'        => date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s") . " + 365 day")),

            ]);

            OmayaUser::create([
                'username'          => $request->input('admin_id'),
                'tenant_id'         => $request->input('tenant_id'),
                'password'          => bcrypt($request->input('password')),
                'role'              => "admin",
                'email'             => $request->input('email'),
                'permission'        => "rw",
            ]);


            OmayaModule::get()->each(function($module) use($request){

                if($module->is_superuser == false) {

                    OmayaRole::create([
                        'name'          => 'admin',
                        'module_id'     => $module->name,
                        'tenant_id'     => $request->input('tenant_id'),
                    ]);
                }


            });


            \App\Processors\SaveLicenseProcessor::make($request->input('license_key'), $request->input('tenant_id'))->execute();


            unset($user);
            unset($uid);


        } catch (ValidationException $e) {
            return redirect(route('admin.cloud.tenant.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.cloud.tenant.edit', [$cloud->id]))
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

        if(!$tenant = OmayaCloud::where('id', $id)->first()){

            return redirect(route('admin.cloud.tenant.index'))
                ->withErrors(trans('alert.record-not-found'));

        }

        return view('v1.admin.clouds.tenants.edit', compact('tenant'));
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
        
        try {   


            if(!$cloud = OmayaCloud::where('id', $id)->first()){

                return redirect(route('admin.cloud.tenant.index'))
                    ->withErrors(trans('alert.record-not-found'));

            }

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'name'                  => 'min:4|max:50|required',
                'license_key'           => 'nullable',
            ];


            $this->validate($request, $rules);
            unset($rules);

            if(!empty($request->input('license_key'))){

                
                $license = checkLicense($request->input('license_key'));
                
                if(!$license["result"]){
                    
                    return back()->withErrors(['license_key' => 'License key is not valid.'])->withInput();
                }
                
                if($license['client_name'] != $cloud->tenant_id){
                    
                    return back()->withErrors([
                        'license_key' => 'Tenant ID entered not same as license ',
                        'tenant_id' => 'Tenant ID entered not same as license '
                        ])->withInput();
                        
                }
            }

            ############
            ## END VALIDATION PART
            ############


            $user = \Auth::user();


            $data = array(
                'name'              => $request->input('name'),
                'email'             => $request->input('email'),
                'phone'             => $request->input('phone'),
                'address'           => $request->input('address'),
                'updated_by'        => $user->id,
            );
     
            if(!empty($request->input('license_key'))){
                $data['license_key']    =   $request->input('license_key');
                $data['expired_at']     =   date('Y-m-d H:i:s', $license['expire_on']);

            }             
            
            $cloud->update($data);

            if($request->input('license_key')) \App\Processors\SaveLicenseProcessor::make($request->input('license_key'), $cloud->tenant_id)->execute();


            unset($user);


        } catch (ValidationException $e) {
            return redirect(route('admin.cloud.tenant.edit', [$cloud->id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.cloud.tenant.edit', [$cloud->id]))
            ->withSuccess(trans('alert.success-update'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($name, Request $request)
    {
        // if($request->ajax()){

        //     if(!$check = OmayaCloud::where('name', $name)->where('name', '<>', 'default')->first()){
        //         return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
        //     }

        //     $check->delete();
        //     return response()->json(['status' => 'ok']);
        // }

        return \Response::json(['status' => 'fail']);
    }

    public function ajaxSuspend($name, Request $request){

        if($request->ajax()){

            if(!$check = OmayaCloud::where('name', $name)->where('name', '<>', 'default')->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $check->update(['is_active' => $check->is_active ? false : true]);

            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);

    }

}
