<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Setting;

class ModelSettingTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetNonexistentSetting_shouldReturnFalse()
    {
        $this->assertFalse( Setting::getByName('nonExistentSetting') );
    }

    public function testGetAfterSet_shouldReturnCorrectValue()
    {
        $settingName = 'someSetting' . uniqid();
        $settingValue = 'someValue' . uniqid( md5(time()), true );

        Setting::setByName($settingName, $settingValue );

        $this->assertEquals($settingValue, Setting::getByName($settingName));
    }
}