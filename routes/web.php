<?php

use App\Http\Controllers\v1\Admin\Alerts\AlertController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsBenchmarkController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsCrossPathController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsCrossvisitController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsDwelltimeController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsEntryExitController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsHeatmapController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsLiveMapController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsLoyaltyDistributionController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsUniqueVisitController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsVenueMapController;
use App\Http\Controllers\v1\Admin\Analytics\AnalyticsVisitController;
use App\Http\Controllers\v1\Admin\Auth\LoginController;
use App\Http\Controllers\v1\Admin\Clouds\CloudTenantController;
use App\Http\Controllers\v1\Admin\Dashboards\DashboardController;
use App\Http\Controllers\v1\Admin\Helps\HelpToolController;
use App\Http\Controllers\v1\Admin\Manages\ManageDeviceApController;
use App\Http\Controllers\v1\Admin\Manages\ManageDeviceTrackerController;
use App\Http\Controllers\v1\Admin\Manages\ManageEntityController;
use App\Http\Controllers\v1\Admin\Manages\ManageGroupController;
use App\Http\Controllers\v1\Admin\Manages\ManageLocationController;
use App\Http\Controllers\v1\Admin\Manages\ManageLocationMapController;
use App\Http\Controllers\v1\Admin\Manages\ManageRuleController;
use App\Http\Controllers\v1\Admin\Manages\ManageVenueController;
use App\Http\Controllers\v1\Admin\Manages\ManageZoneController;
use App\Http\Controllers\v1\Admin\Monitors\MonitorDeviceApController;
use App\Http\Controllers\v1\Admin\Monitors\MonitorDeviceTrackerController;
use App\Http\Controllers\v1\Admin\Monitors\MonitorSchedulerController;
use App\Http\Controllers\v1\Admin\Monitors\MonitorServiceController;
use App\Http\Controllers\v1\Admin\Monitors\MonitorVenueController;
use App\Http\Controllers\v1\Admin\Settings\SettingAdministratorController;
use App\Http\Controllers\v1\Admin\Settings\SettingCMXConfigController;
use App\Http\Controllers\v1\Admin\Settings\SettingConfigController;
use App\Http\Controllers\v1\Admin\Settings\SettingFilteringController;
use App\Http\Controllers\v1\Admin\Settings\SettingIntegrationController;
use App\Http\Controllers\v1\Admin\Settings\SettingRolesController;
use App\Http\Controllers\v1\Admin\Settings\SettingServiceController;
use App\Http\Controllers\v1\Admin\Settings\SettingUserController;
use App\Http\Controllers\v1\Admin\Settings\SettingUserDeviceController;
use App\Http\Controllers\v1\Admin\Settings\SettingVenueController;
use App\Http\Controllers\v1\Admin\Settings\SettingVenueDeviceController;
use App\Http\Controllers\v1\Admin\Settings\SettingVenueMapController;
use App\Http\Controllers\v1\Admin\WebController;
use App\Models\OmayaDeviceController;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaLocation;
use App\Models\OmayaVenue;
use App\Models\OmayaZone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    if(config('app.env') == "production"){

        return redirect(route('admin.login'));
        
    }

    dd(session()->all());

});


Route::get('/migrate', function () {


    abort(403);
    // $omy_db = new mysqli("127.0.0.1", "omaya_main", "", "jrww", 3306);


    // JRWW AP
    // $ap = $omy_db->query("SELECT * FROM device_access_points");
    // $ap = $ap->fetch_all(MYSQLI_ASSOC);


    // echo "total ap - " . count($ap). "\n";

    // $total = 0;

    //     $omy_user = \Auth::user()->id;
    // foreach ($ap as $key => $value) {

    //     $cur = OmayaDeviceController::where('name', $value['name'])->first();

    //     if($cur) continue;


    //     do {

    //         $uid = randomStringId();

    //     } while (OmayaDeviceController::where('device_uid', $uid)->first());




    //     $j_loc = $omy_db->query("SELECT * FROM locations where location_uid = '{$value['location_uid']}'");
    //     $j_loc = $j_loc->fetch_all(MYSQLI_ASSOC)[0];

        
    //     if(!$location = OmayaLocation::where('name', $j_loc['name'])->first()){
    //             echo json_encode($j_loc);
    //             dd(123);
    //         continue;
    //     }

    //     $j_ven = $omy_db->query("SELECT * FROM venues where venue_uid = '{$value['venue_uid']}'");
    //     $j_ven = $j_ven->fetch_all(MYSQLI_ASSOC)[0];

    //     if(!$venue = OmayaVenue::where('name', $j_ven['name'])->first()){
    //             echo json_encode($j_ven);
    //             dd(123);

    //         continue;
    //     }

    //     if(!empty($value['zone_uid'])) {

    //         $j_zone = $omy_db->query("SELECT * FROM zones where zone_uid = '{$value['zone_uid']}'");
    //         $j_zone = $j_zone->fetch_all(MYSQLI_ASSOC)[0];

    //         if(!$zone = OmayaZone::where('name', $j_zone['name'])->first()){
    //             continue;
    //         }

    //     }

    //     $mac_address = str_replace([':', '-'], '', $value['mac_address']);

    //     OmayaDeviceController::create([
    //         'tenant_id'                 => session('tenant_id'),
    //         'location_uid'              => $location->location_uid,
    //         'venue_uid'                 => $venue->venue_uid,
    //         'zone_uid'                  => $zone->zone_uid ?? NULL,
    //         'device_uid'                => $uid,
    //         'device_type'               => "huawei",
    //         'name'                      => $value['name'],
    //         'mac_address'               => strtoupper($mac_address),
    //         'mac_address_separator'     => strtoupper(str_replace("-", ":", $mac_address)),
    //         'rssi_min'                  => $venue->rssi_min,
    //         'rssi_max'                  => $venue->rssi_max,
    //         'rssi_min_ble'              => $venue->rssi_min_ble,
    //         'rssi_max_ble'              => $venue->rssi_max_ble,
    //         'dwell_time'                => $venue->dwell_time,
    //         'is_default_setting'        => true,
    //         'is_active'                 => true,
    //         'created_by'                => $omy_user,
    //         'updated_by'                => $omy_user,
    //     ]);

    //     $total++;

    // }


    // echo "Success ap - " . $total ."\n";



    // ===================================================



    // $ap = $omy_db->query("SELECT * FROM device_access_points");
    // $ap = $ap->fetch_all(MYSQLI_ASSOC);


    // echo "total ap - " . count($ap). "\n";

    // $total = 0;

    //     $omy_user = \Auth::user()->id;
    // foreach ($ap as $key => $value) {

    //     $cur = OmayaDeviceController::where('name', $value['name'])->first();

    //     $cur->position_x = $value['position_x'];
    //     $cur->position_y = $value['position_y'];
    //     $cur->mac_address_separator = strtoupper(str_replace("-", ":", $value['mac_address']));
    //     $cur->save();
    //     $total++;

    // }
    // echo "Success ap - " . $total ."\n";

    // ==============================================================



    // $tag = $omy_db->query("SELECT * FROM tags");
    // $tag = $tag->fetch_all(MYSQLI_ASSOC);


    // echo "total tag - " . count($tag). "\n";


    // $omy_user = \Auth::user()->id;

    // foreach ($tag as $key => $value) {

    //     do {

    //         $uid = randomStringId();

    //     } while (OmayaDeviceTracker::where('device_uid', $uid)->first());




    //     $mac_address = str_replace([':', '-'], '', $value['mac_address']);

    //     OmayaDeviceTracker::create([
    //         'tenant_id'                 => session('tenant_id'),
    //         'device_uid'                => $uid,
    //         'name'                      => $value['alias'],
    //         'remarks'                   => $value['allocation'],
    //         'mac_address'               => strtoupper($mac_address),
    //         'mac_address_separator'     => strtoupper(str_replace("-", ":", $value['mac_address'])),
    //         'is_active'                 => true,
    //         'created_by'                => $omy_user,
    //         'updated_by'                => $omy_user,
    //     ]);

    // }


    

});

Route::group(['prefix' => 'admin'], function () {


    Route::get('/'      , [LoginController::class, 'view']);
    Route::get('login'  , [LoginController::class, 'view'])->name('admin.login');
    Route::post('login' , [LoginController::class, 'loginVerify'])->name('admin.login.verify');

    // Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout')->middleware('auth');
    Route::get('logout' , [LoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

    Route::post('set-web-mode' , [WebController::class, 'webMode'])->name('admin.set-web-mode')->middleware('auth');
    Route::post('clear-cache' , [WebController::class, 'clearCache'])->name('admin.clear-cache')->middleware('auth');


    Route::get('/setting/user/change-password', [WebController::class, 'editPassword'])->name('admin.user.change-password')->middleware('auth');
    Route::post('update-password', [WebController::class, 'updatePassword'])->name('admin.user.update-password')->middleware('auth');
    Route::get('/setting/user/profile', [WebController::class, 'profile'])->name('admin.user.profile')->middleware('auth');
    Route::post('update-profile', [WebController::class, 'updateProfile'])->name('admin.user.update-profile')->middleware('auth');


    Route::post('check-notification', [WebController::class, 'checkNotification'])->name('admin.user.check-notification')->middleware('auth');
    
    Route::post('accept-notification', [WebController::class, 'acceptNotification'])->name('admin.user.accept-notification')->middleware('auth');


    Route::get('alert', [AlertController::class,'index'])->name('admin.alert');

    Route::middleware(['auth', 'IsAbleTo'])->group(function () {

        // Switch/Change Tenant
        Route::post('change-tenant', [WebController::class, 'changeTenant'])->name('admin.web.change-tenant');


        //dashboard
        Route::get('dashboard', [DashboardController::class,'index'])->name('admin.dashboard');
        Route::post('dashboard/data', [DashboardController::class,'data'])->name('admin.dashboard.data');


        //management
        Route::group(['prefix' => 'manage'], function () {

            Route::resource('location'  ,  ManageLocationController::class, ['as' => 'admin.manage']);
            Route::resource('location-map'  ,  ManageLocationMapController::class, ['as' => 'admin.manage']);
            Route::resource('venue'     ,  ManageVenueController::class, ['as' => 'admin.manage']);
            Route::get('venue/{id}/map' ,  [ManageVenueController::class, 'mapView']);
            Route::post('venue/map'     ,  [ManageVenueController::class, 'saveLocationPosition'])->name('admin.manage.venue.ap-position');
            Route::resource('zone'      ,  ManageZoneController::class, ['as' => 'admin.manage']);
            Route::post('list-venue'    ,  [ManageZoneController::class, 'listVenue'])->name('admin.info.list-venue');
            Route::post('list-zone'     ,  [ManageZoneController::class, 'listZone'])->name('admin.info.list-zone');
            
            Route::get('device-ap/import'  ,  [ManageDeviceApController::class, 'importFile'])->name('admin.manage.device.import');
            Route::get('device-ap/export'  ,  [ManageDeviceApController::class, 'exportFile'])->name('admin.manage.device.export'); 
            Route::resource('device-ap' ,  ManageDeviceApController::class, ['as' => 'admin.manage']);
            Route::post('device-ap/import' ,  [ManageDeviceApController::class, 'insertFile'])->name('admin.manage.device.insert-import');


            Route::post('location-map/save-position', [ManageLocationMapController::class , 'savePosition'])->name('admin.management.location-map.save-position');



            Route::resource('device-tracker' ,  ManageDeviceTrackerController::class, ['as' => 'admin.manage']);
            Route::resource('group' ,  ManageGroupController::class, ['as' => 'admin.manage']);
            Route::resource('entity' ,  ManageEntityController::class, ['as' => 'admin.manage']);
            Route::resource('rule' ,  ManageRuleController::class, ['as' => 'admin.manage']);

            Route::post('ajax/rule/get-type' ,  [ManageRuleController::class, 'getType'])->name('admin.manage.rule.ajax.get-type');
            Route::post('ajax/rule/get-event' ,  [ManageRuleController::class, 'getEvent'])->name('admin.manage.rule.ajax.get-event');
            Route::post('ajax/rule/get-venue' ,  [ManageRuleController::class, 'getVenue'])->name('admin.manage.rule.ajax.get-venue');
            Route::post('ajax/rule/get-zone' ,  [ManageRuleController::class, 'getZone'])->name('admin.manage.rule.ajax.get-zone');


        });


        //Monitor
        Route::group(['prefix' => 'monitor'], function () {

            Route::resource('device-ap'    ,  MonitorDeviceApController::class, ['as' => 'admin.monitor']);
            Route::resource('device-tracker' ,MonitorDeviceTrackerController::class, ['as' => 'admin.monitor']);
            Route::resource('venue' , MonitorVenueController::class, ['as' => 'admin.monitor']);
            Route::resource('service'    ,  MonitorServiceController::class, ['as' => 'admin.monitor']);
            Route::resource('scheduler'    ,  MonitorSchedulerController::class, ['as' => 'admin.monitor']);

            Route::post('device-tracker/load-data', [MonitorDeviceTrackerController::class , 'loadData']);
            Route::post('venue/load-data', [MonitorVenueController::class , 'loadData']);

            // Route::post('device-ap/get-statistic'    , [MonitorDeviceApController::class, 'getStatistic'])->name('admin.monitor.device-ap.statistic');

        });


        //Monitor
        Route::group(['prefix' => 'help'], function () {

            Route::get('service'    ,  [HelpToolController::class, 'service'])->name('admin.help.service.index');
            Route::post('service/restart'    ,  [HelpToolController::class, 'serviceRestart'])->name('admin.help.service.restart');


            Route::get('device-blacklist'    ,  [HelpToolController::class, 'deviceBlacklist'])->name('admin.help.device-blacklist.index');
            Route::get('device-blacklist/blacklist/{mac_address}'    ,  [HelpToolController::class, 'blacklist'])->name('admin.help.device-blacklist.blacklist');
            Route::get('device-blacklist/whitelist/{mac_address}'    ,  [HelpToolController::class, 'whitelist'])->name('admin.help.device-blacklist.whitelist');

        });


       


        //setting
        Route::group(['prefix' => 'setting'], function () {

            //role
            Route::resource('role',  SettingRolesController::class, ['as' => 'admin.setting']);
            Route::get('role-data', [SettingRolesController::class, 'dataTable'])->name('admin.setting.role.data');
            Route::get('ajax-filter', [SettingRolesController::class, 'ajax_filter'])->name('admin.setting.role.ajax_filter');

            //user
            Route::resource('user',  SettingUserController::class, ['as' => 'admin.setting']);
            Route::get('user-data', [SettingUserController::class, 'dataTable'])->name('admin.setting.user.data');
            
            //config
            Route::get('config/{tab?}', [SettingConfigController::class, 'index'])->name('admin.setting.config.index');
            Route::post('config-timezone', [SettingConfigController::class, 'config_timezone'])->name('admin.setting.config.timezone');
            Route::post('config-smtp', [SettingConfigController::class, 'smtp'])->name('admin.setting.config.smtp');
            Route::post('config-test-smtp', [SettingConfigController::class, 'smtpTest'])->name('admin.setting.config.smtp-test');
            Route::post('mall-module', [SettingConfigController::class, 'mall_module'])->name('admin.setting.config.mall');
            Route::post('dwell-time', [SettingConfigController::class, 'dwell_time'])->name('admin.setting.config.dwell');
            
            //filtering
            Route::get('filtering', [SettingFilteringController::class, 'index'])->name('admin.setting.filtering.index');
            Route::post('auto-filter-venue', [SettingFilteringController::class, 'update_auto_filter_venue'])->name('admin.setting.filtering.update_auto_filter_venue');
            Route::post('ajax-auto-filter-venue', [SettingFilteringController::class, 'ajax_auto_filter_venue'])->name('admin.setting.filtering.ajax_auto_filter_venue');
            Route::post('update', [SettingFilteringController::class, 'update'])->name('admin.setting.filtering.update');
            Route::post('ajax_update_oui_list', [SettingFilteringController::class, 'ajax_update_oui_list'])->name('admin.setting.filtering.ajax_update_oui_list');


            Route::resource('service',  SettingServiceController::class, ['as' => 'admin.setting']);

        });
        
        //cloud
        Route::group(['prefix' => 'cloud'], function () {

            Route::resource('tenant', CloudTenantController::class, ['as' => 'admin.cloud']);
            Route::get('tenant-data', [CloudTenantController::class, 'dataTable'])->name('admin.cloud.tenant.data');
            Route::post('/{id}/ajax-suspend', [CloudTenantController::class, 'ajaxSuspend'])->name('admin.cloud.tenant.suspend');
            
        });
        
        //analytics
        Route::group(['prefix' => 'analytic'], function () {

            Route::get('heatmap', [AnalyticsHeatmapController::class, 'index'])->name('admin.analytics.heatmap.index');
            Route::post('heatmap/data', [AnalyticsHeatmapController::class, 'data'])->name('admin.analytics.heatmap.data');


            Route::get('benchmark', [AnalyticsBenchmarkController::class, 'index'])->name('admin.analytics.benchmark.index');
            Route::post('benchmark/data', [AnalyticsBenchmarkController::class, 'data'])->name('admin.analytics.benchmark.data');
            Route::post('benchmark/heatmap', [AnalyticsBenchmarkController::class, 'heatmap'])->name('admin.analytics.benchmark.heatmap');


            Route::get('cross-visit', [AnalyticsCrossvisitController::class, 'index'])->name('admin.analytics.cross_visit.index');
            Route::post('cross-visit/data', [AnalyticsCrossvisitController::class, 'data'])->name('admin.analytics.cross_visit.data'); 


            Route::get('visit', [AnalyticsVisitController::class, 'index'])->name('admin.analytics.visit.index');
            Route::post('visit/data', [AnalyticsVisitController::class, 'data'])->name('admin.analytics.visit.data'); 



            Route::get('dwell-time', [AnalyticsDwelltimeController::class, 'index'])->name('admin.analytics.dwell_time.index');
            Route::post('dwell-time/data', [AnalyticsDwelltimeController::class, 'data'])->name('admin.analytics.dwell_time.data'); 
            Route::get('venue-map', [AnalyticsVenueMapController::class, 'index'])->name('admin.analytics.venue_map.index');
            Route::post('venue-map/data', [AnalyticsVenueMapController::class, 'data'])->name('admin.analytics.venue_map.data'); 




            // Route::get('heatmap', [AnalyticsBenchmarkController::class, 'index'])->name('admin.analytics.benchmark.index');
            // Route::post('heatmap/data', [AnalyticsBenchmarkController::class, 'data'])->name('admin.analytics.benchmark.data');


            // Route::get('cross_visit', [AnalyticsCrossvisitController::class, 'index'])->name('admin.analytics.cross_visit.index');
            // Route::get('dwell-time', [AnalyticsDwelltimeController::class, 'index'])->name('admin.analytics.dwell_time.index');
            // Route::post('dwell_time/data', [AnalyticsDwelltimeController::class, 'data'])->name('admin.analytics.dwell_time.data');
            Route::get('loyalty_distribution', [AnalyticsLoyaltyDistributionController::class, 'index'])->name('admin.analytics.loyalty_distribution.index');
            Route::get('unique_visit', [AnalyticsUniqueVisitController::class, 'index'])->name('admin.analytics.unique_visit.index');
            Route::get('visit', [AnalyticsVisitController::class, 'index'])->name('admin.analytics.visit.index');
            Route::get('cross_path', [AnalyticsCrossPathController::class, 'index'])->name('admin.analytics.cross_path.index');
            Route::get('enter_exit', [AnalyticsEntryExitController::class, 'index'])->name('admin.analytics.enter_exit.index');
            // Route::get('venue_map', [AnalyticsVenueMapController::class, 'index'])->name('admin.analytics.venue_map.index');
            Route::get('venue_map/{id}/heatmap' ,  [AnalyticsVenueMapController::class, 'mapView']);
            Route::get('live_heatmap' ,  [AnalyticsVenueMapController::class, 'liveHeatmap']);

        });
    });

});
