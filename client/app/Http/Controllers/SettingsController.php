<?php
/**
 * Invoicer settings management controller
 */

namespace App\Http\Controllers;

use App\InvoicerApi;

class SettingsController extends InvoicerController {

    public function index()
    {
        return view('settings.list', ['settings' => $this->api->settings()]);
    }

} // END class