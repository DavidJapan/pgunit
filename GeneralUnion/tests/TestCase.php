<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

    use CreatesApplication;
    
    //useful switches when running phpunit
    //
    //vendor/bin/phpunit  --process-isolation --testdox
    protected function setVerboseErrorHandler() {
        $handler = function($errorNumber, $errorString, $errorFile, $errorLine) {
            echo "
                ERROR INFO
                Message: $errorString
                File: $errorFile
                Line: $errorLine
                ";
        };
        set_error_handler($handler);
    }

}
