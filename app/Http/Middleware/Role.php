<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * Role 
     * 1 - super admin
     * 2 - admin 
     * 
     **/
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) { // if the current role is Administrator

            $user = User::join('roles', 'users.role', 'roles.id')->find(Auth::user()->id);

            if ($user->incident_only == 1) {
                $route_array = array(
                    'incident',
                    'incident/{incident}',
                    'incidentOverdue',
                    'incidentRemain',
                    'getTask',
                    'getTask/{id}',
                    'getFile/{id}',
                    'getLog',
                    'getLog/{id}',
                    'getHistory',
                    'getHistory/{id}',
                    'getCustomer/{id}',
                    'addTask',
                    'editTask/{id}',
                    'uploadData',
                    'getCustomerHistory/{id}',
                    'getCustomerFile/{id}',
                    'deleteFile/{id}',
                    'logout',
                    'user/profile'
                );
                // $routeName = Route::currentRouteName();
                // dd($routeName);
                $route = Route::getCurrentRoute()->uri;
                //dd($route);
                if (in_array($route, $route_array, false)) {
                    return $next($request);
                } else {

                    return redirect('incident');
                }
            }
        }
        return $next($request);
    }
}
