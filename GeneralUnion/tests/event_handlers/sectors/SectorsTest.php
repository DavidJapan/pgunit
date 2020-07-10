<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\TestHelpers;
use App\Models\Sector;
//https://stackoverflow.com/questions/3841190/phpunit-fatal-error-handling/3842311
//These tests can cause fatal errors so run them with
//phpunit --process-isolation

class SectorsTest extends TestCase {
    public function testSectorCreated(){
        $sector = new Sector();
        $number = random_int(0, 10000);
        $sector->sector = $number . '_sector';
        $sector->save();
        $this->assertEquals($number . '_sector', $sector->sector);
    }
}
