<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use Illuminate\Http\Request;

class DataTableControllerTest extends TestCase {

    private $APP_CONFIG_NAME = 'datatables';

    /** 
     * https://gist.github.com/aphoe/26495499a014cd8daf9ac7363aa3b5bd
     * @param $route
     *
     * @return bool
     */
    function checkRoute($route) {
        if ($route[0] === "/") {
            $route = substr($route, 1);
        }
        $routes = \Route::getRoutes()->getRoutes();
        foreach ($routes as $r) {
            /** @var \Route $r */
            if ($r->uri == $route) {
                return true;
            }
        }

        return false;
    }

    public function testAppConfig() {
        $filename = config_path($this->APP_CONFIG_NAME . '.php');
        $this->assertTrue(file_exists($filename), $this->APP_CONFIG_NAME . '.php doesn\'t exist in the config directory');
        if (file_exists($filename)) {
            $config = config($this->APP_CONFIG_NAME);
            //print_r($config);
            $this->assertTrue(array_key_exists('models', $config), 'The models key doesn\'t exist in the ' . $this->APP_CONFIG_NAME . ' configuration.');
        }
    }

    public function testControllerTraits() {
        $this->assertTrue(trait_exists('App\Traits\AddItem'), 'AddItem doesn\'t exist.');
        $this->assertTrue(trait_exists('App\Traits\EditItem'), 'EditItem doesn\'t exist.');
        $this->assertTrue(trait_exists('App\Traits\ParentTrait'), 'AddItem doesn\'t exist.');
        $this->assertTrue(trait_exists('App\Traits\SSP'), 'SSP doesn\'t exist.');
    }

    public function testControllers() {
        $this->assertTrue(class_exists('App\Http\Controllers\DataTableController'), 'DataTableController doesn\'t exist.');
        $this->assertTrue(class_exists('App\Http\Controllers\EditableDataTableController'), 'EditableDataTableController doesn\'t exist.');
    }

    public function testModelTraits() {
        $this->assertTrue(trait_exists('App\Models\AddItemTrait'), 'AddItemTrait doesn\'t exist.');
        $this->assertTrue(trait_exists('App\Models\EditItemTrait'), 'EditItemTrait doesn\'t exist.');
        $this->assertTrue(trait_exists('App\Models\EditInlineTrait'), 'EditInlineTrait doesn\'t exist.');
    }

    public function testClasses() {
        $this->assertTrue(class_exists('App\AppClasses\Tools\PathToModelMapper'), 'PathToModelMapper doesn\'t exist.');
        $this->assertTrue(class_exists('App\AppClasses\DataTableModelException'), 'DataTableModelException doesn\'t exist.');
        $this->assertTrue(class_exists('App\AppClasses\DataTableColumn'), 'DataTableColumn doesn\'t exist.');
        $this->assertTrue(class_exists('App\AppClasses\DataControl'), 'DataControl doesn\'t exist.');
        $this->assertTrue(class_exists('App\AppClasses\DbMetadata'), 'DbMetadata doesn\'t exist.');
        $this->assertTrue(class_exists('App\Models\DataTable'), 'DataTable doesn\'t exist.');
        $this->assertTrue(class_exists('App\Models\EditableDataTable'), 'EditableDataTable doesn\'t exist.');       
    }

}
