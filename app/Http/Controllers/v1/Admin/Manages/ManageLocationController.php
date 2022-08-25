<?php

namespace App\Http\Controllers\v1\Admin\Manages;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use DataTables;
use Illuminate\Http\Request;


class ManageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            
            // $raw_count  = 1;
            // $zones      = OmayaLocation::select('location_uid', 'name', 'updated_at')->orderByDesc('updated_at')->get()->each(function($data) use(&$raw_count){


            //     $data->raw_count        = $raw_count; $raw_count++;
            //     $data->responsive_id    = "";

            //     $action_btn  = "";
            //     $action_btn .= able_to("management", "zone", "r") ? editCustomButton(route('admin.manage.zone.edit', [$data->location_uid])) : "";
            //     $action_btn .= able_to("management", "zone", "rw") ? deleteCustomButton(route('admin.manage.zone.destroy', [$data->location_uid])) : "";

            //     $data->action = actionCustomButton($action_btn);

            // });


            // return json_encode(["data" => $zones]);



            // // FOR DATATABLE
            // $data = OmayaLocation::select('name','updated_at', 'location_uid');
            // return Datatables::of($data)
            //     ->addIndexColumn()
            //     ->addColumn('action', function($data){
            //         $action_btn  = "";

            //         $action_btn .= able_to("manage", "location", "rw") ? editCustomButton(route('admin.manage.location.edit', [$data->location_uid])) : "";
            //         $action_btn .= able_to("manage", "location", "rw") ? deleteCustomButton(route('admin.manage.location.destroy', [$data->location_uid])) : "";
        
            //         return actionCustomButton($action_btn);

            //     })
            //     ->addColumn('responsive_id', function($data){
            //         return "";
            //     })
            //     ->rawColumns(['action', 'responsive_id'])
            //     ->make(true);

        }else {

            $locations = OmayaLocation::select('location_uid', 'name', 'updated_at')->get();
            return view('v1.admin.manages.locations.index', compact('locations'));

        }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('v1.admin.manages.locations.create');
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

            // START VALIDATION
            $omy_rules = [
                'name'      => 'required|min:3|max:200',
                'address'   => 'max:300',
                'remark'    => 'max:300',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            if(OmayaLocation::where('name', $request->input("name"))->first()){
                return back()->withErrors(['name' => 'Name has been taken'])->withInput();
            }

            //END VALIDATION

            $omy_user = \Auth::user()->id;


            do {

                $uid = randomStringId();

            } while (OmayaLocation::where('location_uid', $uid)->first());




            $omy_location = OmayaLocation::create([
                'tenant_id'     => session('tenant_id'),
                'location_uid'  => $uid,
                'name'          => $request->input('name'),
                'address'       => $request->input('address'),
                'remark'        => $request->input('remark'),
                'created_by'    => $omy_user,
                'updated_by'    => $omy_user,
            ]);



        }
        catch (ValidationException $e) {
            return redirect(route('admin.manage.location.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.location.edit', $omy_location->location_uid))
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
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        if(!$omy_location = OmayaLocation::where('location_uid', $id)->first()) {

            return redirect(route('admin.manage.location.index'))->withErrors(trans('alert.record-not-found'));

        }

        return view('v1.admin.manages.locations.edit', compact('omy_location'));

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

        if(!$omy_location = OmayaLocation::where('location_uid', $id)->first()){

            return redirect(route('admin.manage.location.index'))->withErrors(trans('alert.record-not-found'));

        }

        try{

            // START VALIDATION
            $omy_rules = [
                'name'      => 'required|min:3|max:200',
                'address'   => 'max:300',
                'remark'    => 'max:300',
            ];
            
            $this->validate($request, $omy_rules);
            unset($omy_rules);

            if(OmayaLocation::where('name', $request->input("name"))->where('id', '!=', $omy_location->id)->first()){
                return back()->withErrors(['name' => 'Name has been taken'])->withInput();
            }

            //END VALIDATION

            $omy_user = \Auth::user()->id;



            $omy_location->update([
                'name'          => $request->input('name'),
                'address'       => $request->input('address'),
                'remark'        => $request->input('remark'),
                'updated_by'    => $omy_user,
            ]);



        }
        catch (ValidationException $e) {
            return redirect(route('admin.manage.location.edit', $omy_location->location_uid))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.manage.location.edit', $omy_location->location_uid))
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

            if(!$omy_location = OmayaLocation::where('location_uid', $id)->first()){

                return \Response::json(['status' => 'fail', 'message' => trans('alert.record-not-found')]);
            }


            // START CHECKING DATA USE IN OTHER TABLE
            $omy_module = [];
            if(OmayaVenue::where('location_uid', $id)->first()) $omy_module[] = "Venue";
 
            if(OmayaZone::where('location_uid', $id)->first()) $omy_module[] = "Zone";

            if(OmayaDeviceController::where('location_uid', $id)->first()) $omy_module[] = "Device ['AP']";


            if(!empty($omy_module))
            return \Response::json(['status' => 'fail', 'message' => trans('alert.error-delete-data-use', ["module" => implode(", " , $omy_module)])]);

            // END CHECKING DATA USE IN OTHER TABLE


            $omy_location->delete();
            return response()->json(['status' => 'ok']);
        }

        return \Response::json(['status' => 'fail']);
    }
}
