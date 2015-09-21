<?php
/**
 *
 */

namespace App\Http\Controllers;

use App\InvoicerApi;

class InvoicerController extends Controller {

    protected $api = null;

    public function __construct()
    {
        $this->api = $api = new InvoicerApi(env('INVOICER_ENDPOINT'), env('INVOICER_KEY'));
    }

}