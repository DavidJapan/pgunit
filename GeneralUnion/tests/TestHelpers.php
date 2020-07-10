<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests;

/**
 * Description of TestHelpers
 *
 * @author David Mann
 */
class TestHelpers {

    /**
     * @deprecated
     * Adapted from
     * https://gist.github.com/aphoe/26495499a014cd8daf9ac7363aa3b5bd
     * 
     * @param type $routes We pass this using 
     * \Route::getRoutes()->getRoutes(); 
     * to get all the available routes.
     * @param type $route This is just a uri that we want to test
     * @return boolean
     */
    public static function _checkRoute($routes, $route) {
        if ($route[0] === "/") {
            $route = substr($route, 1);
        }
        foreach ($routes as $r) {
            /** @var \Route $r */
            if ($r->uri == $route) {
                return true;
            }
        }
        return false;
    }

    public static function checkRoute($specified_route) {
        //$counter = 0;
        foreach (\Route::getRoutes()->getIterator() as $test_route) {
            //fwrite(STDERR, print_r($test_route->uri() . "\n", TRUE));    
            if ($test_route->uri() === $specified_route) {                
                //fwrite(STDERR, print_r($test_route->uri(), TRUE));    
                return true;
            }else{
                //fwrite(STDERR, print_r($test_route->uri() . ' Not the specified route'. "\n", TRUE));
                //return false;
            }
        }
        return false;
    }

}
