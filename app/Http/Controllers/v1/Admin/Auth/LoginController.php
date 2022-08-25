<?php

namespace App\Http\Controllers\v1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\OmayaCloud;
use App\Models\OmayaRole;
use App\Models\OmayaWebuser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;     



    protected $redirectTo = '/admin/dashboard';//RouteServiceProvider::HOME;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {

        return view('v1.admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginVerify(Request $request)
    {


        $this->validateLogin($request);

        // CUSTOM CHECK LICENSE OMAYA
        $licenses = $this->omayaLicenseValidation($request);

        // if($request->input("username") == "superuser") $request->input("tenant_id") = "superuser";

        if(isset($licenses["result"]) && $licenses["result"] == false){

            return back()->withInput()->withErrors([$licenses["error-msg"]]);
        }



        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

    }

    
   /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {

        $request->validate([
            'tenant_id'         => (!config('general.multi_tenant') ? '' : 'required|string'),
            $this->username()   => 'required|string',
            'password'          => 'required|string',
        ]);

    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        
        ##GET CLOUD LICENCE AT helper.php
        if($user->tenant_id == "superuser") $user->tenant_id = "default";

        $tenant = getTenantLicense($user->tenant_id);

        Session::put('web_mode'  , $user->web_mode);

        if (!empty($tenant['type'])) {
            
            Session::put('client_name'  , $tenant['client_name']);
            Session::put('omaya_type'   , $tenant['type']);
            Session::put('expire_on'    , $tenant['expire_on']);
            Session::put('generate_on'  , $tenant['generate_on']);
            Session::put('triangulation', $tenant['triangulation']);
            Session::put('device_limit' , $tenant['device_limit']);
            Session::put('heatmap'      , 'enabled'); //value will get from license later

        } 

        Session::put('permission', $user->permission);
        Session::put('role', $user->role);
        Session::put('tenant_id', $user->tenant_id);


        if($cloud = OmayaCloud::where('tenant_id', $user->tenant_id)->first()){
            Session::put('timezone', $cloud->timezone);
            Session::put('name', $cloud->name);
        }


        if($user->role != "superuser") {

            
            $modules        = OmayaRole::where('tenant_id', $user->tenant_id)->where('name', $user->role)->get();
            // $venue_assign   = OmayaRole::where('tenant_id', $user->tenant_id)->where('name', $user->role)->groupBy('name')->pluck('allowed_venue_id')->first();
            // $venue_assign   = explode(',', $venue_assign);

            $access_group   = [];
            foreach ($modules as $key => $module) {

                $omy_temp       = trim($module['module_id']);
                $omy_temp_group = trim(explode(':', $omy_temp)[0]);

                if (!in_array($omy_temp_group, $access_group)) $access_group[] =  $omy_temp_group;


                $access_list[] = $omy_temp;

            }

            Session::put('access_list', $access_list);
            Session::put('access_group', $access_group);
            // Session::put('venue_assign', $venue_assign);


        }



    } 

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');

        $credentials["tenant_id"] = ($request->input($this->username()) == "superuser" ? "superuser" : ($request->input('tenant_id') ?? config('general.tenant_name')) );

        return $credentials;
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $message = "You have been successfully logout.";

        $type = "success";
        if(request()->has('message')) {
            $message = request()->message;
        }


        if(request()->has('type')) {
            $type = request()->type;
        }   


        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        if($type == "success") {

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->route('admin.login')->withSuccess($message);
        }else{ 

            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->route('admin.login')->withErrors($message);


        }
    }

    public function omayaLicenseValidation($request)
    {

        $omy['tenant-id']       = $request->input('tenant_id') ?? 'default';


        // FOR SUPERUSER ONLY
        if($omy['tenant-id'] == "default" && $request->input('username') == "superuser") {

            return ["result" => true , "error-msg" => ""];

        }


        if ($omy['tenant-id'] != "superuser") {

            ## GET TENANT LICENCE AT helper.php
            $omy['tenant-license']  = getTenantLicense($omy['tenant-id']);

            if(isset($omy["tenant-license"]["result"]) && $omy["tenant-license"]["result"] == false) 
                return ["result" => false , "error-msg" => $omy['tenant-license']["error-msg"]];

            if(!OmayaCloud::where('tenant_id', $omy['tenant-id'])->where('is_active', 1)->first()) {
                return ["result" => false , "error-msg" => "You tenant was suspended by administrator."];

            }
        }




        return ["result" => true , "error-msg" => ""];



    }

}
