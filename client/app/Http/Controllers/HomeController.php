<?php
/**
 *
 */

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

class HomeController extends InvoicerController {

    public function index()
    {
        $from = date('Y-m-1', strtotime('-1 year'));
        $to = date('Y-m-d');

        $invoices = $this->api->invoices($from, $to);
        $settings = $this->api->settings();

        $monthly = $this->api->monthlyTotals( $invoices );

        return view('home.dashboard', [
            'invoices'  => $invoices,
            'totals'    => $this->api->totals($invoices),
            'settings'  => $settings,
            'monthly'   => $monthly,
        ]);
    }

} // END class