<?php

namespace App\Http\Controllers\v1\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\OmayaCloud;
use App\Models\OmayaConfig;
use App\Models\OmayaVenue;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class SettingConfigController extends Controller
{
    public function index($tab = 'license'){

        $active_tab = $tab;

        //license key tab
        $today      = new DateTime();
        $date       = new DateTime(date('Y-m-d H:i:s', session('expire_on')));
        $interval   = $today->diff($date);
        $intervalstring     = formatDateDiff($date, $today);

        // $license = getTenantLicense(session('tenant_id'))

        // $total_device = VenueDev::get();
        // $total_device = $total_device->count(); 

        $license_validity   =  $date > $today ? 'Valid' : 'invalid';

        // //for timezone tab
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first();

        // //for mall module tab 
        // $venue = OmayaVenue::where('tenant_id', session('tenant_id'));
        // if(is_array(session('venue_assign')) && session('venue_assign')[0] != '') $venue = $venue->whereIn('venue_uid', session('venue_assign'));
        // $venue = $venue->get();

        // $mall_module = OmayaConfig::where('config_name','mall_module')->first();
        // $ent_venue = OmayaConfig::where('config_name','entrance_venue')->pluck('value')->first();
        // $ent_venue = explode(',', $ent_venue);

        // //dwell time tab
        // $dwell = OmayaConfig::where('config_name','dwell_time')->first();

        return view('v1.admin.settings.config.index', compact('intervalstring', 'license_validity','tzlist', 'active_tab', 'cloud'));
    }

    public function license(Request $request){
       
        $active_tab = 'license';

    }

    public function config_timezone(Request $request){

        $active_tab = 'timezone';

        if(!$cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first()){

            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors(trans('alert.record-not-found'));

        }

        try{

            $rules = [
                'timezone'   => 'required',
                'keep_log' => 'required|integer|min:1|max:30'
            ];

            $this->validate($request, $rules);
            unset($rules);

            $cloud->update([
                'timezone' => $request->timezone,
                'delete_log' => $request->keep_log
            ]);

            unset($cloud);

            \Session::put('timezone'   , $request->timezone);


        }catch (ValidationException $e) {
            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.config.index', $active_tab))
            ->withSuccess(trans('alert.success-update'));
    }


    public function smtp(Request $request){


        $active_tab = 'smtp';

        if(!$cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first()){

            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors(trans('alert.record-not-found'));

        }

        try{

            $rules = [
                'smtp_host'                 => 'nullable',
                'smtp_port'                 => 'integer|nullable|',
                'smtp_authentication'       => 'nullable',
                'smtp_username'             => 'nullable',
                'smtp_password'             => 'nullable',
                'smtp_from_email_address'   => 'nullable',
                'smtp_from_name'            => 'nullable',
            ];

            $this->validate($request, $rules);

            unset($rules);

            $cloud->update([
                'smtp_is_active'            => $request->input('is_active') ? true : false,
                'smtp_host'                 => $request->input('smtp_host'),
                'smtp_port'                 => $request->input('smtp_port'),
                'smtp_auth'                 => $request->input('smtp_authentication') ? $request->input('smtp_authentication') : "none",
                'smtp_username'             => $request->input('smtp_username'),
                'smtp_password'             => $request->input('smtp_password'),
                'smtp_from_email'           => $request->input('smtp_from_email_address'),
                'smtp_from_name'            => $request->input('smtp_from_name'),
            ]);

            unset($cloud);

            session(['timezone' => $request->timezone]);



        }catch (ValidationException $e) {
            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.config.index', $active_tab))
            ->withSuccess(trans('alert.success-update'));

    }



    public function mall_module(Request $request){

        $active_tab = 'mall';

        try{

            $rules = [
                'mall_module'       => 'sometimes',
                'entrance_venue'    => 'required_if:mall_module,1',
            ];

            $this->validate($request, $rules);
            unset($rules);

            OmayaConfig::where('config_name', 'mall_module')->update([
                'value' => $request->has('mall_module') ? 'enabled' : 'disabled',
            ]);

            OmayaConfig::where('config_name', 'entrance_venue')->update([
                'value' => is_array($request->entrance_venue) && count($request->entrance_venue) > 0 ?  implode(',',$request->entrance_venue) : '',
            ]);


        }catch (ValidationException $e) {
            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.config.index', $active_tab))
            ->withSuccess(trans('alert.success-update'));

    }

    public function dwell_time(Request $request){

        $active_tab = 'dwell';

        try{

            $rules = [
                'dwell'       => 'required',
            ];

            $this->validate($request, $rules);
            unset($rules);

            OmayaConfig::where('config_name', 'dwell_time')->update([
                'value' => $request->dwell,
            ]);


        }catch (ValidationException $e) {
            return redirect(route('admin.setting.config.index', $active_tab))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('admin.setting.config.index', $active_tab))
            ->withSuccess(trans('alert.success-update'));

    }


    public function smtpTest(Request $request){


        if ($request->ajax()) {

            if($omy_cloud = OmayaCloud::where('tenant_id', session('tenant_id'))->first()) {


                if(empty($omy_cloud->smtp_host) || empty($omy_cloud->smtp_port)) {

                    return respondAjax("warning", "Please config your smtp first");

                }

                $omy_smtp = [];
                $omy_smtp['host']       = $omy_cloud->smtp_host;
                $omy_smtp['port']       = $omy_cloud->smtp_port;
                $omy_smtp['auth']       = $omy_cloud->smtp_auth;
                $omy_smtp['username']   = $omy_cloud->smtp_username;
                $omy_smtp['password']   = $omy_cloud->smtp_password;
                $omy_smtp['from_email'] = $omy_cloud->smtp_from_email;
                $omy_smtp['from_name']  = $omy_cloud->smtp_from_name;


                // Recipient
                $recipients = explode(",", $request->input("recipient"));

                foreach ($recipients as $key => $value) {

                    $value = trim($value);

                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {

                        $omy_smtp["to_email"][] = ["email" => $value, "name" => "SMTP Test"];

                    }else {
                        return respondAjax("error", "Please insert a valid email address");

                    }

                }


                $omy_smtp['from_name']  = $omy_cloud->smtp_from_name;
                $omy_smtp['subject']    = "[Omaya] SMTP Testing";
                $omy_smtp['body']       = "Succeed! This is a SMTP test email. Generated on: " . date("Y-m-d H:i:s");


                $email_respond = sendMail($omy_smtp);


                return respondAjax("info", $email_respond);
            }



        }


        return respondAjax("error", "Unrecognized request");
    }
}
