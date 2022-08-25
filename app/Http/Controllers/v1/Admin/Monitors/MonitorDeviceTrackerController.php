<?php

namespace App\Http\Controllers\v1\Admin\Monitors;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class MonitorDeviceTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {


            $total["all-active"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_trackers WHERE tenant_id = '" . session("tenant_id") ."' AND is_active = '1'"))[0]->count;


            // COUNT ALL DEVICE 
            $total["all"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_trackers WHERE tenant_id = '" . session("tenant_id") ."' "))[0]->count;

            $total["online"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_trackers WHERE tenant_id = '" . session("tenant_id") ."' AND status = 'active'"))[0]->count;

            $total["no-new"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_trackers WHERE tenant_id = '" . session("tenant_id") ."' AND status = 'no new packet'"))[0]->count;

            $total["offline"] = \DB::select(\DB::raw("SELECT COUNT(id) as count FROM omaya_device_trackers WHERE tenant_id = '" . session("tenant_id") ."' AND status IS NULL"))[0]->count;

            return ["total" => $total];



        }else {


            // $ip =   "192.168.0.82";
            // exec("ping -c 1 -W 5 192.168.0.38", $output, $status);
            // dd($status);
            // exit();
            $devices = OmayaDeviceTracker::with('entity')->orderByDesc('last_seen_at')->where('is_active', true)->get();

            return view('v1.admin.monitors.device-trackers.index', compact('devices'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadData(Request $request)
    {


        if($request->input('device_uid')){


            if($device = OmayaDeviceTracker::select('name', 'mac_address', 'device_uid')->where('tenant_id', session('tenant_id'))->where('device_uid', $request->input('device_uid'))->first()) {


                $omy_cache          = redisCache();

                $omy_key = $omy_cache->hGetAll("DEVICE:TRACKER:PRESENT:". session('tenant_id') .":{$device->mac_address}");


                if(!empty($omy_key)){


                    if($ap = OmayaDeviceController::where('mac_address', $omy_key['mac_address_ap'])->first()) {



                        if($venue = OmayaVenue::where('venue_uid', $ap->venue_uid)->first()) {

                            if(empty($venue->image)) 
                                return ["status" => false, "data" => [], "message" => "Please upload image at management venue."];


                            $zone = OmayaZone::select('name')->where('zone_uid', $ap['zone_uid'])->first();
                            $location = OmayaLocation::select('name')->where('location_uid', $ap['location_uid'])->first();


                            $omy_key['last_detected'] = converTimeToLocal($omy_key['last_detected'], session('timezone'), "d M Y h:i:s a");
                            $omy_key['location'] = "{$location->name} -> {$venue->name} " . (empty($zone) ? "" : "-> {$zone->name}");
                            // $omy_key['venue'] = $venue->name;
                            // $omy_key['zone'] = $zone->name;
                            $omy_key['ap_name'] = $ap->name . " [{$ap->mac_address}]";


                            $ap_list = OmayaDeviceController::select('device_uid', 'position_x', 'position_y', 'name', 'mac_address')->where('venue_uid', $ap["venue_uid"])->get()->each(function($data) use($omy_key){

                                $data->status = "";
                                if($data->mac_address == $omy_key['mac_address_ap'])
                                    $data->status = "current";


                            });



                            $center_x   = $venue->image_width / 2;
                            $center_y   = $venue->image_height / 2;


                            if(($ap->position_x < $center_x && $ap->position_y < $center_y)){ //top left marker

                                $device->position_x = $ap->position_x + rand(9,99);
                                $device->position_y = $ap->position_y + rand(9,99);
                            }

                            elseif($ap->position_x > $center_x && $ap->position_y < $center_y) { //top right marker

                                $device->position_x = $ap->position_x - rand(9,99);
                                $device->position_y = $ap->position_y + rand(9,99);
                            }
                            elseif($ap->position_x < $center_x && $ap->position_y > $center_y) { //bottom left marker
                                $device->position_x = $ap->position_x + rand(9,99);
                                $device->position_y = $ap->position_y - rand(9,99);
                            }
                            elseif($ap->position_x > $center_x && $ap->position_y > $center_y) { //bottom right marker
                                $device->position_x = $ap->position_x - rand(9,99);
                                $device->position_y = $ap->position_y - rand(9,99);
                            }


                
                            return ["status" => true, "data" => ["venue" => $venue, "device-ap" => $ap_list, "device-tracker" => $device, "location" => $location , "zone" => $zone, "device_cache" => $omy_key]];

                        }

                        return ["status" => false, "data" => [], "message" => "Venue not found for device [AP]."];


                    }




                    // $ble_now['last_location'] = $ble_now["location_name"] ." <i class='fa fa-arrow-right'></i> " . $ble_now["venue_name"] ." <i class='fa fa-arrow-right'></i> " . $ble_now["zone_name"] ;



                    $ap = DeviceAccessPoint::where('mac_address', implode("-", str_split($ble_now['ap_mac_address'], 2)))->first();


                    if($ap) {


                        if($venue = Venue::where('tenant_id', session('tenant_id'))->where('venue_uid', $ap["venue_uid"])->first()) {

                            $venue->image_url           = $venue->getImageUrl(session('tenant_id'));
                            $venue->image_thumbnail_url = $venue->getThumbnailImageUrl(session('tenant_id'));


                            $zones = Zone::select('id', 'zone_uid', 'points', 'color', 'name')->where('tenant_id', session('tenant_id'))->where('venue_uid', $ap["venue_uid"])->get();


                            $ap_list = DeviceAccessPoint::select('device_access_point_uid', 'position_x', 'position_y', 'name', 'mac_address')->where('venue_uid', $ap["venue_uid"])->get()->each(function($data) use($ble_now){

                                $data->status = "";
                                if(str_replace(array(":", "-"), "", $data->mac_address) == $ble_now["ap_mac_address"])
                                    $data->status = "current";


                            });




                            $center_x   = $venue->image_width / 2;
                            $center_y   = $venue->image_height / 2;


                            if(($ap["position_x"] < $center_x && $ap["position_y"] < $center_y)){ //top left marker

                                $tag->position_x = $ap["position_x"] + rand(9,99);
                                $tag->position_y = $ap["position_y"] + rand(9,99);
                            }

                            elseif($ap["position_x"] > $center_x && $ap["position_y"] < $center_y) { //top right marker

                                $tag->position_x = $ap["position_x"] - rand(9,99);
                                $tag->position_y = $ap["position_y"] + rand(9,99);
                            }
                            elseif($ap["position_x"] < $center_x && $ap["position_y"] > $center_y) { //bottom left marker
                                $tag->position_x = $ap["position_x"] + rand(9,99);
                                $tag->position_y = $ap["position_y"] - rand(9,99);
                            }
                            elseif($ap["position_x"] > $center_x && $ap["position_y"] > $center_y) { //bottom right marker
                                $tag->position_x = $ap["position_x"] - rand(9,99);
                                $tag->position_y = $ap["position_y"] - rand(9,99);
                            }




                    
                
                            return ["status" => true, "data" => ["venue" => $venue, "zones" => $zones, "ap" => $ap_list, "cache_tag" => $ble_now, "tag" => $tag ], "message" => ""];

                        }

                        return ["status" => false, "data" => [], "message" => "[101] Please check your cache data."];


                    }

                    return ["status" => false, "data" => [], "message" => "[102] Please check your cache data."];

                }


                return ["status" => false, "data" => [], "message" => "Device is not detected by any devices [AP]."];

            }
            return ["status" => false, "data" => [], "message" => "Device not found."];


                
        }
        return ["status" => false, "data" => [], "message" => "Please select device."];

    }

    
}
