<?php

namespace App\Http\Middleware;

use Closure;

class IsAbleTo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(time() > session('expire_on') ) {

            return redirect('admin/logout?message=your license already expired. Please contact the administrator for more details.&type=danger');

        }
        $currentAction  = \Route::currentRouteAction();
        $path_full      = url()->previous();
        $allow          = true;


        if($currentAction){

            list($controller, $method) = explode('@', $currentAction);
            $controller = preg_replace('/.*\\\/', '', $controller);

            switch ($method) {

                case 'dataTable':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'r');

                    break;


                case 'index':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'r');

                    break;

                case 'create':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'rw');

                    break;

                case 'store':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'rw');

                    break;

                case 'edit':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'r');

                    break;

                case 'update':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'rw');

                    break;

                case 'destroy':

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'rw');

                    break;    

                default :

                    $allow = $this->able_to(config('permission_action.module_name.' . $controller), 'r', $method);
                    break;

            }
        }

        if(!$allow) {

            $msg = "You are not allowed to access or proceed the action. Please contact your administrator for more details.";
            if(!$request->ajax()){

                return redirect($path_full)->with(["allow-access" => "false", "warning-message" => $msg]);
            }else{

                return \Response::json(['status' => false, "message" => $msg]);

            }
        }

        return $next($request);
    }


    public function able_to($module_name, $permission, $method = "")
    {

        if (auth()->user()->role != 'superuser') {

            $module_arr     = explode(':', $module_name);
            $group_name     = $module_arr[0];

            if(!in_array($group_name, session('access_group'))){
                return false;
            }

            if(in_array(config('permission_action.module_name.' . $method), session('access_list'))) {
                return true;
            }
           

            if(!in_array($module_name, session('access_list'))){
                return false;
            }

            if(auth()->user()->permission == 'r' && $permission == 'rw') {

                return false;
            }

        }

        return true;



    }
}
