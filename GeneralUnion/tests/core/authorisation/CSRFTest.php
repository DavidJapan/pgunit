<?php

namespace Tests\Unit;

use Tests\TestCase;

//use \Illuminate\Contracts\Foundation\Application;
//use Illuminate\Routing\Router as Router;
//use Illuminate\Contracts\Events\Dispatcher; 
//Trying to get CSRF working in a AJAX context is really hard to configure right. 
class CSRFTest extends TestCase {

    public function testMiddleWareGroups() {
        $propertyName = 'middlewareGroups';
        $app = app();
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        /*
         * 
         */
        $reflection = new \ReflectionClass(get_class($kernel));
        //$method = $reflection->getMethod($methodName);
        $this->assertTrue(property_exists($kernel, $propertyName), $propertyName . ' doesn\'t exist.');
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $middlewareGroups = $property->getValue($kernel);
        $this->assertTrue($middlewareGroups['web'][4] === 'App\Http\Middleware\VerifyCsrfToken', 'The App\Http\Middleware\VerifyCsrfToken has not been added to the Kernel middleware groups web array.');
        //fwrite(STDERR, print_r($middlewareGroups['web'][4], TRUE));
    }
    /**
     * I think by the time this test runs, the csrf token has actually been assigned so I can't test for
     * the presence of the literal string @csrf but only the value that Laravel has assigned to the variable
     */
    public function testCSRFTokenPresent(){
        $filepath = resource_path('views/authorised/index.blade.php');
        //fwrite(STDERR, print_r($filepath, TRUE));
        //$this->assertTrue(exec('grep @csrf '. $filepath), '@csrf is not present in the index blade');    
        $this->assertTrue(strpos(file_get_contents($filepath), '@csrf') !== false);
    }
}
