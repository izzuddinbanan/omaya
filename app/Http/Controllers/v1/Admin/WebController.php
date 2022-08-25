<?php

namespace App\Http\Controllers\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OmayaCloud;
use App\Models\OmayaDeviceController;
use App\Models\OmayaDeviceTracker;
use App\Models\OmayaNotification;
use App\Models\OmayaNotificationHistory;
use App\Models\OmayaRole;
use App\Models\OmayaUser;
use Illuminate\Http\Request;

class WebController extends Controller
{
    
    public function webMode(Request $request)
    {
            
        if($request->input('cur_layout')) {

            \Session::put('web_mode'  , $request->input('cur_layout'));

            $user = \Auth::user();
            $user->update([
                'web_mode' => $request->input('cur_layout')
            ]);

        }
    }


    public function editPassword()
    {

        return view('v1/admin/settings/user/change_password');

    }

    public function updatePassword(Request $request)
    {

        try{

            ## Start Validation 


            $rules = [
                'old_password'          => 'min:4|max:50|required',
                'new_password'          => 'min:4|max:50|required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])[A-Za-z0-9_@.\/#&+\-\*]{8,}$/',
                'confirm_new_password'  => 'same:new_password|required',
            ];

            $this->validate($request, $rules);
            unset($rules);

            //confirmation new password
            if($request->input('new_password') != $request->input('confirm_new_password') ){
                return back()->withErrors(['confirm_new_password' => 'New password did not match!'])->withInput();
            }

            //new password cannot same as old password
            if($request->input('new_password') == $request->input('old_password') ){
                return back()->withErrors(['new_password' => 'New password cannot be same as older password!'])->withInput();
            }

            $user = \Auth::user();

            //check inserted old password with database match or not
            if(!\Hash::check($request->old_password, $user->password)){
                return back()->withErrors(['old_password' => 'Please check again your current password!'])->withInput();
            }
            
            ## End Validation

            $user->update([
                'password' => bcrypt($request->new_password)
            ]);

            unset($user);

        }
        catch (ValidationException $e) {
            return redirect(route('admin.user.change-password'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.user.change-password'))
            ->withSuccess('Password successfully updated');


    }

    public function profile(){
        
        $user = \Auth::user();
        
        return view('v1.admin.settings.user.profile', compact('user'));
    }

    public function updateProfile(Request $request){

        $user = \Auth::user();
        if(!$user = OmayaUser::where('tenant_id', session('tenant_id'))->where('id', $user->id)->first()) {

            return redirect(route('admin.dashboard'))->withErrors(trans('alert.record-not-found'));

        }

        try {

            ###########
            ## START VALIDATION PART
            ###########

            $rules = [
                'username'  => 'min:4|max:200',
                'email'     => 'email',
                'photo'     => 'image|mimes:jpg,png,jpeg|max:5000',
            ];


            $this->validate($request, $rules);
            unset($rules);

            if(OmayaUser::where('tenant_id', session('tenant_id'))->where('username', $request->username)->where('id', '!=', $user->id)->first()){
                return back()->withErrors(['username' => 'Username has been taken'])->withInput();
            }


            ############
            ## END VALIDATION PART
            ############

            $user->update([
                'username'  => $request->username,
                'email'     => $request->email
            ]);



            if ($request->hasFile('photo')) {
                $path_image = 'app/public/tenants/' . session('tenant_id') . '/user/';

                ### DELETE IMAGE ORIGINAL AND THUMNAIL 
                if ($user->photo != null) {
                    \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/user/' . $user->photo));
                    \File::delete(public_path('storage/tenants/' . session('tenant_id') . '/user/thumbnails/' . removeStringAfterCharacters($user->photo) . '.jpg'));
                }

                ## STORE IMAGE
                $user->photo           = \App\Processors\SaveImageProcessor::make($request->file('photo'), $path_image)->execute();
                $user->save();
            }


        } catch (ValidationException $e) {
            return redirect(route('admin.user.profile'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.user.profile'))
            ->withSuccess(trans('alert.success-create'));

    }



    public function clearCache(Request $request)
    {

        // return \Response::json(['status' => 'fail', 'data' => '']);
        if($request->ajax()){

            $omy_cache = redisCache();


            do {

                $omy_cache_clear = $omy_cache->scan($omy_keys, "*:*:" . session('tenant_id'));

                foreach($omy_cache_clear as $omy_cache_key){

                    $omy_cache->del($omy_cache_key);

                }

            } while ($omy_keys != 0);

            unset($omy_cache_clear, $omy_cache_key, $omy_keys);



            // Reset device registerd with tenant
            OmayaDeviceController::select('mac_address')->get()->each(function($ap) use($omy_cache){

                $omy_cache->del("DEVICE:AP:DATA:" . $ap->mac_address);

            });

            $omy_cache->del("AP:COUNT:" . session('tenant_id'));



            do {

                $omy_cache_clear = $omy_cache->scan($omy_keys, "DEVICE:TRACKER:DATA:" . session('tenant_id') . ":*");

                foreach($omy_cache_clear as $omy_cache_key){

                    $omy_cache->del($omy_cache_key);

                }

            } while ($omy_keys != 0);

            unset($omy_cache_clear, $omy_cache_key, $omy_keys);


            $omy_cache->del("DEVICE:RULES:" . session('tenant_id'));


            $devices = OmayaDeviceTracker::get()->toArray();
            foreach ($devices as $device) {
                
                $omy_cache->hMSet("DEVICE:TRACKER:DATA:" . session('tenant_id') . ":{$device['mac_address']}", $device);

            }
            unset($devices);




            // COPY FROM LOGIN // REFRESH SESSION 
            $user = \Auth::user();
            $tenant = getTenantLicense(session('tenant_id'));

            \Session::put('web_mode'  , $user->web_mode);

            if (!empty($tenant['type'])) {
                
                \Session::put('client_name'  , $tenant['client_name']);
                \Session::put('omaya_type'   , $tenant['type']);
                \Session::put('expire_on'    , $tenant['expire_on']);
                \Session::put('generate_on'  , $tenant['generate_on']);
                \Session::put('triangulation', $tenant['triangulation']);
                \Session::put('device_limit' , $tenant['device_limit']);
                \Session::put('heatmap'      , 'enabled'); //value will get from license later

            } 

            \Session::put('permission', $user->permission);
            \Session::put('role', $user->role);


            if($cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first()){
                \Session::put('timezone', $cloud->timezone);
                \Session::put('name', $cloud->name);
            }


            if($user->role != "superuser") {

                
                $modules        = OmayaRole::where('tenant_id', session('tenant_id'))->where('name', $user->role)->get();
                $access_group   = [];
                foreach ($modules as $key => $module) {

                    $omy_temp       = trim($module['module_id']);
                    $omy_temp_group = trim(explode(':', $omy_temp)[0]);

                    if (!in_array($omy_temp_group, $access_group)) $access_group[] =  $omy_temp_group;


                    $access_list[] = $omy_temp;

                }

                \Session::put('access_list', $access_list);
                \Session::put('access_group', $access_group);



                $omy_cache->del("TENANT:DATA:". session('tenant_id'));
                $omy_cache->del("TENANT:LICENSE:". session('tenant_id'));



            }else {



                do {

                    $omy_cache_clear = $omy_cache->scan($omy_keys, "TENANT:DATA:*");

                    foreach($omy_cache_clear as $omy_cache_key){

                        $omy_cache->del($omy_cache_key);

                    }

                } while ($omy_keys != 0);

                unset($omy_cache_clear, $omy_cache_key, $omy_keys);



                do {

                    $omy_cache_clear = $omy_cache->scan($omy_keys, "TENANT:LICENSE:*");

                    foreach($omy_cache_clear as $omy_cache_key){

                        $omy_cache->del($omy_cache_key);

                    }

                } while ($omy_keys != 0);

                unset($omy_cache_clear, $omy_cache_key, $omy_keys);


            }
            // END COPY FROM LOGIN // REFRESH SESSION 


            \Session::forget("list_tenant");


            if($user->role == "superuser") {

                \DB::query("FLUSH QUERY CACHE"); 
                \Artisan::call('config:cache');
                \Artisan::call('view:cache');
                \Artisan::call('optimize');
                
            }

            return response()->json(['status' => 'ok']);

        }

        return \Response::json(['status' => 'fail', 'data' => '']);
    }

    public function changeTenant(Request $request)
    {
        

        if($request->ajax()){


            if(!$omy_cloud = OmayaCloud::where('tenant_id', $request->input('tenant_id'))->first()) {
              
                return \Response::json(['status' => 'fail', 'message' => 'Tenant id not found.']);

            }
            \Session::put('tenant_id', $omy_cloud->tenant_id);

            // $user = \Auth::user();
            $tenant = getTenantLicense($omy_cloud->tenant_id);

            if (!empty($tenant['type'])) {
                
                \Session::put('client_name'  , $tenant['client_name']);
                \Session::put('omaya_type'   , $tenant['type']);
                \Session::put('expire_on'    , $tenant['expire_on']);
                \Session::put('generate_on'  , $tenant['generate_on']);
                \Session::put('triangulation', $tenant['triangulation']);
                \Session::put('device_limit' , $tenant['device_limit']);
                \Session::put('heatmap'      , 'enabled'); //value will get from license later

            } 

            \Session::put('timezone', $omy_cloud->timezone);
            \Session::put('name', $omy_cloud->name);

            \Session::forget("list_tenant");


            return \Response::json(['status' => 'success']);

        }

        return \Response::json(['status' => 'fail', 'message' => 'Unknown request.']);
    }



    public function checkNotification(Request $request)
    {

        if($request->ajax()){

            $omy_cache = redisCache();

            $omy_it = NULL;

            $omy_result = "";


            $omy_alerts = [];
            do {

                $omy_alert_keys = $omy_cache->scan($omy_keys, "OMAYA:ALERT:" . session('tenant_id') .":*:" . \Auth::user()->role);


                foreach($omy_alert_keys as $omy_alert_key){
                    
                    $omy_alerts[] = $omy_alert_key;

                }

            } while ($omy_keys != 0);


            if(!empty($omy_alerts)) {


                $omy_result = $omy_cache->hGetAll($omy_alerts[0]);

                $omy_alert = explode(":", $omy_alerts[0]);

                if($notification = OmayaNotification::where('notification_uid', $omy_alert[3])->where('status', 'new')->first()) {


                    $notification->tracker = $notification->tracker;
                    $notification->controller = $notification->controller;
                    $notification->location = $notification->location;
                    $notification->venue = $notification->venue;
                    $notification->zone = $notification->zone;
                    $notification->entity = $notification->entity;
                    $notification->rule = $notification->rule;

                    $notification->trigger_at = getDateLocal($notification->trigger_at);

                    return respondAjax("success", "", ['notification' => $notification]);


                }



            }

            return respondAjax("fail", "", ["data" => ""]);

        }
        

        abort(403);

    }


    public function acceptNotification(Request $request)
    {
        if($notification = OmayaNotification::where('notification_uid', $request->notification_uid)->where('status', 'new')->first()) {


            $omy_cache = redisCache();
            
            do {

                $omy_alert_keys = $omy_cache->scan($omy_keys, "OMAYA:ALERT:" . session('tenant_id') .":*:*");


                foreach($omy_alert_keys as $omy_alert_key){
                    
                    $omy_cache->del($omy_alert_key);

                }

            } while ($omy_keys != 0);

            $notification->update(['status' => 'closed']);


            OmayaNotificationHistory::create([
                'tenant_id' => session('tenant_id'),
                'notification_uid' => $request->input('notification_uid'),
                'status' => 'closed',
                'created_by' => \Auth::user()->id,
                'updated_by' => \Auth::user()->id,
            ]);

        }


        return back()->withSuccess(trans('alert.success-update'));


    }
}
