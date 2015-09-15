<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SettingApiTest extends TestCase {

    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createApplication();
    }

    public function testSavingSetting_returnsSuccess()
    {
        $this->put('/v1/setting/someSetting', ['value' => 'someValue'] )->seeJsonContains(['status' => 'success', 'code' => 0]);
    }

    public function testRetrievingASavedSetting_correctlyReturnsIt()
    {
        $this->put('/v1/setting/someSetting', ['value' => 'someValue'] )->seeJsonContains(['status' => 'success', 'code' => 0]);

        $this->get('/v1/setting/someSetting')->seeJsonContains(['status' => 'success', 'code' => 0, 'data' => ['value' => 'someValue']]);
    }

    public function testListOfSettings_correctlyListsThem()
    {
        $this->put('/v1/setting/primarySetting', ['value' => 'someValue'] )->seeJsonContains(['status' => 'success', 'code' => 0]);
        $this->put('/v1/setting/secondarySetting', ['value' => 'someOtherValue'] )->seeJsonContains(['status' => 'success', 'code' => 0]);

        $this->get('/v1/setting')->seeJsonContains(['status' => 'success', 'code' => 0, 'data' => [
            'primarySetting'    => 'someValue',
            'secondarySetting'  => 'someOtherValue'
        ]]);
    }
} // END class