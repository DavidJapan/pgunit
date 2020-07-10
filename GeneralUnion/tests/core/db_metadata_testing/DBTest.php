<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

class DBTest extends TestCase {
 

    public function testDbMetaDataControllerExists() {
        $this->assertTrue(class_exists('App\Http\Controllers\DBMetaDataController'), 'DBMetaDataController doesn\'t exist.');
        if (class_exists('App\Http\Controllers\DBMetaDataController')) {
            $this->assertTrue(true, 'Ready to test for methods');
            $db = new \App\Http\Controllers\DBMetaDataController();
            $this->assertTrue(method_exists($db, 'getFunctionColumnDataTypes'), 'The getFunctionColumnDataTypes method does not exist');
            $this->assertTrue(method_exists($db, 'buildDisplayTable'), 'The buildDisplayTable method does not exist');
            $this->assertTrue(method_exists($db, 'pgunitTestAll'), 'The pgunitTestAll method does not exist');
            $this->assertTrue(method_exists($db, 'pgunitTestSchema'), 'The pgunitTestSchema method does not exist');
            
            
        }
    }
    public function testDbTestRoutes() {
        $this->assertTrue(TestHelpers::checkRoute('pgunit/test_all'), 'The route pgunit/test_all doesn\'t exist.');
    }
    public function testDbViews() {
        $filename = resource_path('views/tables');
        $this->assertTrue(file_exists($filename), 'The tables directory doesn\'t exist in the views directory');
        if (file_exists($filename)) {
            $filename = resource_path('views/tables/pgunit.php');
            $this->assertTrue(file_exists($filename), 'The pgunit file doesn\'t exist in the tables directory');
        }
    }
}
