<?php

namespace App\Http\Controllers\v1\Admin\Monitors;

use App\Http\Controllers\Controller;
use App\Models\OmayaDeviceController;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Illuminate\Http\Request;

class MonitorVenueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {


            // if(!$location = OmayaLocation::where('location_uid', $request->input('location'))->first()) {

            //     return respondAjax("error", "Location not found.");
            // }
            $venues = OmayaVenue::where('location_uid', $request->input('location'))->pluck('venue_uid', 'name')->toArray();
            
            if(empty($venues)) 
            return respondAjax("info", "No venue found for this location [ {$location->name} ].");


            if($request->input('search')) {

                $search_venues = [];
                $search = strtolower($request->input('search'));
                foreach ($venues as $key => $value) {

                    if(str_contains(strtolower($key), $search)) $search_venues[$value] = $value;
                    
                }


                foreach ($venues as $key => $value) {

                    $search_zones = OmayaZone::where('venue_uid', $value)->select('name')->get();

                    foreach ($search_zones as $search_zone) {
                        if(str_contains(strtolower($search_zone->name), $search)) $search_venues[$value] = $value;
                    }



                }


            }else {

                $search_venues = $venues;

            }

            $venues = OmayaVenue::whereIn('venue_uid', $search_venues)->get();


            $html = "";
            foreach ($venues as $key => $value) {
                $html .= '<div class="card ecommerce-card">
                            <div class="item-img text-center">
                                    <img src="'. $value->thumbnail_image_url .'" class="img-fluid" alt="img-placeholder" />
                            </div>
                            <div class="card-body">
                                <div class="item-name">
                                    <a href="javascript:void()" style="cursor:default !important;">'. $value->name .'</a>
                                </div>
                            </div>
                            <div class="item-options text-center">
                                <button type="button" class="btn btn-primary btn-cart view-venue-map" id="venue_'. $value->venue_uid .'">
                                    <i data-feather="eye"></i>
                                    <span class="">View</span>
                                </button>
                            </div>
                        </div>';
            }

            if(empty($html)) $html = '<div class="demo-spacing-0"><div class="alert alert-primary" role="alert"><div class="alert-body">No Data</div></div></div>';

            return respondAjax("success", "", ["html" => $html]);



        }else {

 
            $locations = OmayaLocation::get();
            
            return view('v1.admin.monitors.venues.index', compact('locations'));
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loadData(Request $request)
    {


        if($request->input('venue_uid')){

            $venue_uid = str_replace("venue_",  "", $request->input('venue_uid'));
            
            $venue = OmayaVenue::where('venue_uid', $venue_uid)->first();

            $controllers = OmayaDeviceController::where('venue_uid', $venue_uid)->get();



            $omy_cache          = redisCache();

            // $key = [];
            // do {

            //     $keys = $omy_cache->scan($omy_scan_venue, "VENUE:TRACKER:PRESENT:". session('tenant_id') .":{$venue->location_uid}:{$venue->venue_uid}:*");

            //     if(empty($keys)) continue;
            //     $key = array_merge($key, $keys);
            // }while ($omy_scan_venue != 0);


            $trackers = OmayaDeviceTracker::get();

            foreach ($controllers as $controller) {


                $devices = [];
                $controller->is_have_tracker = "inactive";


                $pos_exist = [];

                foreach ($trackers as $tracker) {
                    $omy_key = $omy_cache->hGetAll("DEVICE:TRACKER:PRESENT:". session('tenant_id') .":{$tracker->mac_address}");
                    if(empty($omy_key)) continue;

                    if($omy_key["mac_address_ap"] == $controller->mac_address) {



                        $controller->status = "current";
                        $tracker->rssi = $omy_key['rssi'];



                        $center_x   = $venue->image_width / 2;
                        $center_y   = $venue->image_height / 2;

                        do {

                            $posistion_x = rand(9, 99);

                        }while (in_array($posistion_x, $pos_exist));
                        $pos_exist[] = $posistion_x;

                        do {

                            $posistion_y = rand(9, 99);

                        }while (in_array($posistion_y, $pos_exist));

                        $pos_exist[] = $posistion_y;


                        if(($controller->position_x < $center_x && $controller->position_y < $center_y)){ //top left marker

                            $tracker->position_x = $controller->position_x + $posistion_x;
                            $tracker->position_y = $controller->position_y + $posistion_y;
                        }

                        elseif($controller->position_x > $center_x && $controller->position_y < $center_y) { //top right marker

                            $tracker->position_x = $controller->position_x - $posistion_x;
                            $tracker->position_y = $controller->position_y + $posistion_y;
                        }
                        elseif($controller->position_x < $center_x && $controller->position_y > $center_y) { //bottom left marker
                            $tracker->position_x = $controller->position_x + $posistion_x;
                            $tracker->position_y = $controller->position_y - $posistion_y;
                        }
                        elseif($controller->position_x > $center_x && $controller->position_y > $center_y) { //bottom right marker
                            $tracker->position_x = $controller->position_x - $posistion_x;
                            $tracker->position_y = $controller->position_y - $posistion_y;
                        }


            


                        $devices[] = $tracker;

                    }

                }

                $controller->color = "#636363";
                if($controller->status == "current") {
                    $controller->color = "#".$this->random_color();
                }

                $controller->devices = $devices;



            }



            return respondAjax("success", "", ["venue" => $venue, 'device-ap' => $controllers]);

        }
        return ["status" => false, "data" => [], "message" => "Please select venue."];

    }


    function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    function random_color() {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }

}


