<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class RunArtisanTest extends TestCase {


    public function testRunArtisanExists() {
        $this->assertTrue(class_exists('App\Http\Controllers\RunArtisan'), 'RunArtisan doesn\'t exist.');
        if (class_exists('App\Http\Controllers\RunArtisan')) {
            //$this->assertTrue(true, 'Ready to test for methods');
            $navigation = new \App\Http\Controllers\RunArtisan();
            $this->assertTrue(method_exists($navigation, 'cacheConfig'), 'The cacheConfig method does not exist');
            $this->assertTrue(method_exists($navigation, 'clearView'), 'The clearView method does not exist');
            $this->assertTrue(method_exists($navigation, 'cacheRoute'), 'The cacheRoute method does not exist');
        }
    }
    public function testRunArtisanRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('artisan/cache_config'), 'The route artisan/cache_config doesn\'t exist.');
        $this->assertTrue(TestHelpers::checkRoute('artisan/view_clear'), 'The route artisan/view_clear doesn\'t exist.');
        $this->assertTrue(TestHelpers::checkRoute('artisan/cache_route'), 'The route artisan/cache_route doesn\'t exist.');
    }

}
