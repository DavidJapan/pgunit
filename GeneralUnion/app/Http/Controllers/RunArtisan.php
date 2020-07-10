<?php
/**
 * This class enables the administrator to run key artisan commands to clear the cache.
 * Only administrators have access to this method.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * This class enables the administrator to run key artisan commands to clear the cache.
 * Only administrators have access to this method.
 * 
 * The cacheConfig method runs config:cache.
 * The clearView method runs view:clear.
 */
class RunArtisan extends Controller {

    /**
     * Runs the artisan command config:cache
     * @param Request $request
     * @return string
     */
    public function cacheConfig(Request $request) {
        $user = Auth::user();
        $app_name = config('app.name');
        if ($user->hasRole(config($app_name. ".ROLE_ADMINISTRATOR"))) {

            $exitCode = Artisan::call('config:cache');
            if ($exitCode == 0) {
                return "Application configuration successfully cached.";
            } else {
                return json_encode($exitCode);
            }
        }else{
            return "You are not authorised to run this procedure";
        }
    }
    /**
     * Runs the artisan command view:clear
     * @return string
     */
    public function clearView(){
        $user = Auth::user();
        $app_name = config('app.name');
        if ($user->hasRole(config($app_name . ".ROLE_ADMINISTRATOR"))) {
            $exitCode = Artisan::call('view:clear');
            if ($exitCode == 0) {
                return "Views cache successfully cleared.";
            } else {
                return json_encode($exitCode);
            }
        }else{
            return "You are not authorised to run this procedure";
        }         
    }
    /**
     * Runs the artisan command route:cache
     * @param Request $request
     * @return string
     */
    public function cacheRoute(Request $request) {
        $user = Auth::user();
        $app_name = config('app.name');        
        if ($user->hasRole(config($app_name . ".ROLE_ADMINISTRATOR"))) {
            $exitCode = Artisan::call('route:cache');
            if ($exitCode == 0) {
                return "Routes successfully cached.";
            } else {
                return json_encode($exitCode);
            }
        }else{
            return "You are not authorised to run this procedure";
        }
    }
}
