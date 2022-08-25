<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\OmayaUser;
use App\Models\OmayaRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use DataTables;

class SettingUserController extends Controller
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

            // $data = OmayaUser::where('tenant_id', session('tenant_id'))->orderByDesc('updated_at');
            // return Datatables::of($data)
            //     ->addIndexColumn()
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";
            //         $action_btn .= able_to("setting", "user", "r") ? editCustomButton(route('admin.setting.user.edit', [$data->user_uid])) : "";
            //         $action_btn .= able_to("setting", "user", "rw") ? deleteCustomButton(route('admin.setting.user.destroy', [$data->user_uid])) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('responsive_id', function($data){
            //         $responsive_id = "";
            //         return $responsive_id;

            //     })
            //     ->editColumn('created_at', function($data) {
            //         return date('Y-m-d H:i:s', strtotime($data->updated_at));
            //     })
            //     ->rawColumns(['action', 'raw_count', 'responsive_id'])
            //     ->make(true);
        }
        else{

            $users = OmayaUser::where('tenant_id', session('tenant_id'))->get();

            return view('v1.admin.settings.user.index', compact('users'));

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $roles = OmayaRole::groupBy('name')->orderByDesc('name')->get();
        return view('v1.admin.settings.user.create', compact('roles'));
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

            //START VALIDATION
            $rules = [
                'username'      => 'required|min:4|max:200',
                'role'          => 'required',
                'email'         => 'required|email',
                'password'      => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])[A-Za-z0-9_@.\/#&+\-\*]{8,}$/',
                'permission'    => 'required',
            ];
            
            $this->validate($request, $rules);
            unset($rules);

            if(OmayaUser::where('tenant_id', session('tenant_id'))->where('username', $request->username)->first()){
                return back()->withErrors(['username' => 'Username has been taken'])->withInput();
            }

            if($request->username == 'superuser' ){
                return back()->withErrors(['username' => 'This name cannot be use'])->withInput();
            }

            //END VALIDATION

            $curuser = \Auth::user();

            $user = OmayaUser::create([
                'tenant_id'     => session('tenant_id'),
                'email'         => $request->email,
                'username'      => $request->username,
                'password'      => bcrypt($request->input('password')),
                'role'          => $request->role,
                'permission'    => $request->permission,
                'created_by'    => $curuser->id,
                'updated_by'    => $curuser->id,
            ]);



        }
        catch (ValidationException $e) {
            return redirect(route('admin.setting.user.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.user.edit', $user->id))
            ->withSuccess(trans('alert.success-create'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
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
        $user = OmayaUser::where('tenant_id', session('tenant_id'))->where('id', $id)->first();
        $roles = OmayaRole::groupBy('name')->orderByDesc('name')->get();

        return view('v1.admin.settings.user.edit', compact('user', 'roles'));
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

        if(!$user = OmayaUser::where('tenant_id', session('tenant_id'))->where('id', $id)->first()){
            return redirect(route('admin.setting.user.index'))
                ->withErrors(trans('alert.record-not-found'));
        }

        try{

            //START VALIDATION
            $rules = [
                'username'      => 'required|min:4|max:200',
                'role'          => 'required',
                'email'         => 'required|email',
                'password'      => 'nullable|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])[A-Za-z0-9_@.\/#&+\-\*]{8,}$/',
                'permission'    => 'required',
            ];
            
            $this->validate($request, $rules);
            unset($rules);

            if(OmayaUser::where('tenant_id', session('tenant_id'))->where('username', $request->username)->where('id', '!=', $id)->first()){
                return back()->withErrors(['username' => 'Username has been taken'])->withInput();
            }

            if($request->username == 'superuser' ){
                return back()->withErrors(['username' => 'This name cannot be use'])->withInput();
            }



            //END VALIDATION

            $curuser = \Auth::user();

            $data = array(
                'email'         => $request->email,
                'username'      => $request->username,
                'role'          => $request->role,
                'permission'    => $request->permission,
                'updated_by'    => $curuser->id,
            );

            if(!empty($request->password)){
                $data['password']      = bcrypt($request->input('password'));
            }

            $user->update($data);

            unset($curuser);


        }
        catch (ValidationException $e) {
            return redirect(route('admin.setting.user.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.user.edit', $id))
            ->withSuccess(trans('alert.success-create'));

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

            $check = OmayaUser::where('tenant_id', session('tenant_id'))->where('id', $uid);
            
            if(count($check->get()) == 0){
                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $check->delete();
            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }

    
}
