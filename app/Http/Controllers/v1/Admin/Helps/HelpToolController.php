<?php

namespace App\Http\Controllers\v1\Admin\Helps;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceList;
use Illuminate\Http\Request;

class HelpToolController extends Controller
{
    public function service(Request $request)
    {
        
        $services = @file_get_contents("/var/www/omaya/public/storage/omaya-service.json");

        $services = json_decode($services, true);
        if(empty($services)) $services = ["services" => []];

        return view('v1.admin.helps.services.index', compact('services'));
    }

    public function serviceRestart(Request $request)
    {
        if ($request->ajax()) {


            $omy_cache = redisCache();

            $omy_keys   = $omy_cache->set("OMAYA:JOB:RESTART-SERVICE", $request->input('id'), 10);
            
            $wait_reload = 1000;
            if(in_array($request->input('id'), ["redis", "mariadb", "nginx", "php-fpm"]))
                $wait_reload = 5000;

            return \Response::json(['status' => true, "message" => "Service is queue to restart", "wait_reload" => $wait_reload]);

        }

        return \Response::json(['status' => false, "message" => "Please check your request."]);


    }

    public function deviceBlacklist()
    {
        $devices = OmayaDeviceList::get();
        return view('v1.admin.helps.device-blacklists.index', compact('devices'));
    }

    public function whiteList($mac_address)
    {
        
        if($devices = OmayaDeviceList::where('mac_address_device', $mac_address)->update(['is_blacklist' => false])) {



            $omy_cache = redisCache();
            $omy_cache->del("OMAYA:BLACKLIST:{$mac_address}", true);
            $omy_cache->close();

            return redirect(route('admin.help.device-blacklist.index'))->withSuccess(trans('alert.success-update'));

        }
        return redirect(route('admin.help.device-blacklist.index'))->withSuccess(trans('alert.record-not-found'));
    }

    public function blackList($mac_address)
    {
        if($devices = OmayaDeviceList::where('mac_address_device', $mac_address)->update(['is_blacklist' => true])) {


            $omy_cache = redisCache();
            $omy_cache->set("OMAYA:BLACKLIST:{$mac_address}", true);
            $omy_cache->close();

            return redirect(route('admin.help.device-blacklist.index'))->withSuccess(trans('alert.success-update'));
        }

        return redirect(route('admin.help.device-blacklist.index'))->withSuccess(trans('alert.record-not-found'));
    }
}
