<?php

/**
 * Contains method to show the application welcome page and the authorised welcome page. 
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Contains method to show the application welcome page and the authorised welcome page.
 */
class ApiController extends Controller {

    /**
     * Displays the application welcome page.
     *
     * @return \Illuminate\Http\Response\view
     */
    public function getTest() {
        $json = new \stdClass();
        $json->test = 'test';
        return Response::json($json);
    }


    /**
     * Displays the authorised welcome page.
     * @return \Illuminate\Http\Response\view
     */
    public function trigger() {
//event(new \App\Events\Gudb0605Event('hello world'));
        return view('trigger');
    }

    public function ajax(Request $request) {
        $msg = 'This AJAX request triggers the Gudb0605Event.';
        event(new \App\Events\Gudb0605Event($msg));
    }

    /**
     * Displays the authorised welcome page.
     * @return \Illuminate\Http\Response\view
     */
    public function authorised() {
        return view('authorised/index');
    }

}
