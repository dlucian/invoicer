<?php
/**
 * Invoicer settings management controller
 */

namespace App\Http\Controllers;

use App\InvoicerApi;
use Illuminate\Http\Request;
use Validator;

class SettingsController extends InvoicerController {

    public function index()
    {
        return view('settings.list', ['settings' => $this->api->settings()]);
    }

    public function update(Request $request, $settingName)
    {
        return view('settings.update', ['settingName' => $settingName, 'settings' => $this->api->settings()]);
    }

    public function store(Request $request, $settingName)
    {
        $this->api->updateSetting( $settingName, $request->input('value') );
        return redirect(route('settings-list'));
    }

} // END class