<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;

//public function testWelcome() {
//    $this->assertTrue(TestHelpers::checkRoute("/"), 'The route / doesn\'t exist.');
//}


class ConfigTest extends TestCase {

    public function testAppName() {
        $this->assertEquals(config('app.name'), 'datatables', 'The app name is not datatables');
    }

    public function testVersion() {
        $this->assertEquals('0605', config('app.version'), 'The app version is not gudb0605');
    }

    public function testAppBaseDirectoryName() {
        //The inbuilt PHP function basename returns trailing name component of path:
        $app_dir_name = basename(base_path());
        echo $app_dir_name . PHP_EOL;
        $this->assertEquals($app_dir_name, 'GeneralUnion', 'The app base directory name is not GeneralUnion');
        $this->assertTrue(file_exists(config_path('datatables.php')));

        //fwrite(STDERR, print_r($app_dir_name, TRUE));    
    }

    public function testDatabaseSchema() {
        //$pgsql = config('database.connections')['pgsql'];
        //fwrite(STDERR, print_r(json_encode($pgsql), TRUE));     
        $filepath = config_path('database.php');
        $schema_from_env = strpos(file_get_contents($filepath), "'schema' => env('SCHEMA',");
        //fwrite(STDERR, print_r($schema_from_env , TRUE));    
        $this->assertTrue($schema_from_env !== false, 'The database schema is not set in the env file.');
        $app_name = config('app.name');
        $this->assertEquals(config($app_name . '.SCHEMA'), 'gu', 'The schema name is not gu');
    }
    public function testCacheDriver() {
        $this->assertEquals('array', config('cache.default'), 'The default cache should be \'array\'');
    }
    public function testPhpInfo() {
        $this->assertTrue(TestHelpers::checkRoute('phpinfo'), 'The route phpinfo doesn\'t exist.');
        $this->assertTrue(file_exists(resource_path('views/phpinfo.php')), 'phpinfo.php doesn\'t exist in the views directory');
    }
}
