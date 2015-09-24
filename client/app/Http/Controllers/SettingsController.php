<?php
/**
 * Invoicer settings management controller
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        if (!empty($request->input('name')))
            $settingName = $request->input('name');
        if (!empty($settingName))
            $this->api->updateSetting( $settingName, $request->input('value') );
        return redirect(route('settings-list'));
    }

    public function delete(Request $request, $settingName)
    {
        if (!empty($settingName))
            $this->api->deleteSetting( $settingName );
        return redirect(route('settings-list'));
    }

} // END class