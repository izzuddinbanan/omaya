<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OmayaModule;
use App\Models\OmayaVenue;
use App\Models\OmayaRole;

use DataTables;

class SettingRolesController extends Controller
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

            // $data = OmayaRole::groupBy('name');
            // return Datatables::of($data)
            //     ->addIndexColumn()
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";
            //         $action_btn .= able_to("setting", "role", "r") ? editCustomButton(route('admin.setting.role.edit', [$data->name])) : "";
            //         $action_btn .= able_to("setting", "role", "rw") ? deleteCustomButton(route('admin.setting.role.destroy', [$data->name])) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('raw_count', function($data) use($raw_count) {
        
            //         return $raw_count++;

            //     })
            //     ->addColumn('responsive_id', function($data){
            //         $responsive_id = "";
            //         return $responsive_id;

            //     })
            //     ->rawColumns(['action', 'raw_count', 'responsive_id'])
            //     ->make(true);
        }
        else{
            
            $roles = OmayaRole::groupBy('name')
            ->where(function($query){
                if(session('role') != 'superuser'){
                    $query->where('name' ,'!=', 'admin');
                }
            })
            ->get();
            return view('v1.admin.settings.role.index', compact('roles'));
        
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules    = OmayaModule::select('group')->where('is_superuser', false)->groupBy('group')->orderBy('group')->get();
        $submodules = OmayaModule::where('is_superuser', false)->get();

        $venues     = OmayaVenue::get(); 

        return view('v1.admin.settings.role.create', compact('modules','submodules', 'venues'));
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

            $module_access = $request->submodule;
            $rules = [
                'name'  => 'required',
            ];

            $this->validate($request, $rules);
            unset($rules);

            if($request->name == 'superuser' || $request->name == 'admin'){
                return back()->withErrors(['name' => 'This name cannot be use'])->withInput();
            }

            //check unique name
            if(OmayaRole::where('name', $request->name)->first()){
                return back()->withErrors(['name' => 'This name cannot be use'])->withInput();
            }

            if(!isset($request->submodule)){
                return back()->withErrors(['module' => 'Please select access module to this user role'])->withInput();
            }


            $user = \Auth::user();

            foreach($module_access as $module){
                OmayaRole::create([
                    'tenant_id'         => session('tenant_id'),
                    'name'              => $request->name,
                    'module_id'         => $module,
                    'created_by'        => $user->id,
                    'updated_by'        => $user->id,
                ]);
            }

            unset($module_access, $user);

        }catch (ValidationException $e) {
            return redirect(route('admin.setting.role.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.role.edit', [$request->name]))
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
    public function edit($name)
    {
        //check if tenant having role 
        if(count(OmayaRole::where('name', $name)->get()) == 0){
            return redirect(route('admin.setting.role.index'))->withErrors('You do not have ' . $name . ' role access. Please enter new role');
        }

        $modules        = OmayaModule::select('group')->where('is_superuser', false)->groupBy('group')->orderBy('group')->get();
        $submodules     = OmayaModule::where('is_superuser', false)->get();
        $role           = OmayaRole::where('name', $name)->first();
        $module_access  = OmayaRole::where('name', $name)->pluck('module_id')->toArray();
        
        $venue          = OmayaVenue::get(); 
        // $venue_assign   = OmayaRole::where('name', $name)->groupBy('name')->pluck('allowed_venue_id')->first();
        // $venue_assign   = explode(',', $venue_assign);


        return view('v1.admin.settings.role.edit', compact('modules','submodules', 'role', 'module_access', 'venue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $name)
    {
        try{

            $module_access = $request->submodule;
            $rules = [
                'name'  => 'required'
            ];

            $this->validate($request, $rules);
            unset($rules);

            if(($request->name == 'superuser' || $request->name == 'admin') && session('role') != "superuser"){
                return back()->withErrors(['name' => 'This name cannot be use'])->withInput();
            }

            if(!isset($request->submodule)){
                return back()->withErrors(['module' => 'Please select access module to this user role']);
            }

            $user = \Auth::user();

            $check = OmayaRole::where('name', $name);
            if(count($check->get()) > 0){

                //delete first current editing data
                $check->delete();

            }

            
            //new data will re-insert
            foreach($module_access as $module){
                OmayaRole::create([
                    'tenant_id'     => session('tenant_id'),
                    'name'          => $request->name,
                    'module_id'     => $module,
                    'created_by'    => $user->id,
                    'updated_by'    => $user->id,
                ]);
            }

            unset($module_access, $user, $check);


        } catch (ValidationException $e) {
            
            return redirect(route('admin.setting.role.index'))
                ->withErrors($e->getErrors())
                ->withInput();

        }

        return redirect(route('admin.setting.role.edit', [$request->name]))
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
        if($request->ajax()){

            $check = OmayaRole::where('name', $name);
            if(count($check->get()) == 0){
                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }

            $check->delete();
            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }

    public function ajax_filter(Request $request){

        $filter = $request->get('filter');

        $modules    = OmayaModule::select('group')->filter($filter)->groupBy('group')->orderBy('group')->get();
        $submodules = OmayaModule::filter($filter)->select('name')->get();

        
        return ['module'=> $modules,'sub'=> $submodules];

    }
}
