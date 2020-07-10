<?php
/**
 * Contains method to show the application welcome page and the authorised welcome page. 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
/**
 * Contains method to show the application welcome page and the authorised welcome page.
 */
class WelcomeController extends Controller
{

    /**
     * Displays the application welcome page.
     *
     * @return \Illuminate\Http\Response\view
     */
    public function index()
    {
        return view('welcome');
    }
    /**
     * Displays the authorised welcome page.
     * @return \Illuminate\Http\Response\view
     */
    public function trigger(){
        //event(new \App\Events\Gudb0605Event('hello world'));
        return view('trigger');
    }
    public function ajax(Request $request){
        $msg = 'This AJAX request triggers the Gudb0605Event.';
        event(new \App\Events\Gudb0605Event($msg));
    }
    /**
     * Displays the authorised welcome page.
     * @return \Illuminate\Http\Response\view
     */
    public function authorised(){
        return view('authorised/index');
    }
}