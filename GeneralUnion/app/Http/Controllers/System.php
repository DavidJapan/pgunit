<?php
/**
 * Shows the PHP environment
 */
namespace App\Http\Controllers;

/**
 * Shows the PHP environment
 */
class System extends Controller
{

    /**
     * Shows the PHP environment in a view using phpinfo()
     *
     * @return \Illuminate\Http\Response
     */
    public function phpinfo()
    {
        return view('phpinfo');
    }
}