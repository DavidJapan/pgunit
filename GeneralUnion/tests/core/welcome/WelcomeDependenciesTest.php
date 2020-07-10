<?php

namespace Tests\Unit;

use Tests\TestCase;


class WelcomeDependenciesTest extends TestCase {
 
    //Laravel resources test
    public function testNativeAppAssets() {
        $filename = public_path('/css/welcome/bootstrap-3.3.7.css');
        $this->assertTrue(file_exists($filename), 'The bootstrap-3.3.7.css file doesn\'t exist in the css/welcome directory');
    }

    public function testWelcomeControllerExists() {
        $this->assertTrue(class_exists('App\Http\Controllers\WelcomeController'), 'WelcomeController doesn\'t exist.');
        if (class_exists('App\Http\Controllers\WelcomeController')) {
            $this->assertTrue(true, 'Ready to test for methods');
            $welcome = new \App\Http\Controllers\WelcomeController();
            $this->assertTrue(method_exists($welcome, 'index'), 'The index method does not exist');
            $this->assertTrue(method_exists($welcome, 'authorised'), 'The authorised method does not exist');
        }
    }

    public function testWelcomeHomeDeleted() {
        $classname = 'App\Http\Controllers\HomeController';
        $this->assertFalse(class_exists($classname), 'The HomeController exists. You should delete it. ');
        $filename = resource_path('views\home.blade.php');
        $this->assertFalse(file_exists($filename), 'The home blade exists. You should delete it. ');
        $filename = resource_path('views\welcome.blade.php');
        $this->assertFalse(file_exists($filename), 'The welcome blade exists. You should delete it and add app and app_layout. ');
    }

    public function testLaravelAppCssExists() {
        $filename = public_path('/css/app.css');
        $this->assertTrue(file_exists($filename), 'The app.css file doesn\'t exist in the public\css folder.');
        $filename = public_path('/css/vendor.css');
        $this->assertTrue(file_exists($filename), 'The vendor.css file doesn\'t exist in the public\css folder.');
    }

    public function testLogoExists() {
        $filename = public_path('/assets/logo.png');
        $this->assertTrue(file_exists($filename), $filename . ' doesn\'t exist');
    }

    public function testVueBootstrapDeleted() {
        $dirname = resource_path('/js');
        $this->assertTrue(file_exists($dirname), 'The js directory doesn\'t exist in the resources folder.');
        $dirname = resource_path('sass');
        $this->assertFalse(file_exists($dirname), 'The sass directory exists in the resources folder. You should delete it.');
        $filename = public_path('js/app.js');
        $this->assertFalse(file_exists($filename), 'The app.js file exists in the public\js folder. You should delete it. '
                . 'It uses Vue and is not relevant to this application');
    }

}
