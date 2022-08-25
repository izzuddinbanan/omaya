<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\OmayaSystemService;
use Illuminate\Http\Request;

class SettingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $general_services = OmayaSystemService::where('group', 'general')->get();
        $addon_services = OmayaSystemService::where('group', 'add-on')->get();

        return view('v1.admin.settings.services.index', compact('general_services', 'addon_services'));
    }


    public function store(Request $request)
    {
        if ($request->ajax()) {

            if($service = OmayaSystemService::where('id', $request->input('id'))->where('group', 'add-on')->first()) {

                $service->is_enable = $request->input('checked') == "true" ? true : false;
                $service->save();

                $msg = "enabled";
                if($request->input('checked')) $msg = "disabled";

                $remain_time = 60 - (int) date("s");

                return \Response::json(['status' => true, "message" => "Success {$msg} the service. Please wait arround {$remain_time}(s) to reflect the changes."]);

            }

        }

        return \Response::json(['status' => false, "message" => "Please check your request."]);
    }

    
}
