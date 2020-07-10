<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
// vendor/bin/phpunit  --process-isolation --testdox


class WelcomeTest extends TestCase {

    public function testWelcomeRoutes() {
        $this->assertTrue(TestHelpers::checkRoute("/"), 'The route / doesn\'t exist.');
    }

    public function testWelcomeControllerMethods() {
        $fullClassName = 'App\Http\Controllers\WelcomeController';
        $this->assertTrue(class_exists($fullClassName), $fullClassName . ' doesn\'t exist.');
        if (class_exists($fullClassName)) {
            $this->assertTrue(true, 'Ready to test attributes');
            $controller = new $fullClassName();
            $this->assertTrue(method_exists($controller, 'index'));
        }
    }

    public function testWelcomeBlades() {
        $filename = resource_path('views/welcome.blade.php');
        $this->assertTrue(file_exists($filename), 'The welcome blade file doesn\'t exist in the views directory');
        $filename = resource_path('views/app_layout.blade.php');
        $this->assertTrue(file_exists($filename), 'The app_layout blade file doesn\'t exist in the views directory');
    }

    public function testWelcomeJsCssExists() {
        $filename = public_path('/css/welcome/bootstrap-3.3.7.css');
        //Note that we need this old version of bootstrap to maintain the look and feel we established a while ago
        //for the welcome page. The rest of the app uses Bootstrap 4.
        $this->assertTrue(file_exists($filename), 'The ' . $filename . ' file doesn\'t exist in the public\css folder.');
        $filename = public_path('/js/jquery-3.4.1.min.js');
        $this->assertTrue(file_exists($filename), 'The /js/jquery-3.4.1.min.js file doesn\'t exist in the public\js folder.');
        $filename = public_path('/js/welcome/bootstrap.min.js');
        $this->assertTrue(file_exists($filename), 'The /js/welcome/bootstrap.min.js file doesn\'t exist in the public\js folder.');
    }

    public function testLogoExists() {
        $filename = public_path('/assets/logo.png');
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
    }

}
