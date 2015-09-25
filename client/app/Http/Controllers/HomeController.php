<?php
/**
 *
 */

namespace App\Http\Controllers;

use Validator;
use Cache;
use Illuminate\Http\Request;

class HomeController extends InvoicerController {

    public function index()
    {
        $from = date('Y-m-1', strtotime('-1 year'));
        $to = date('Y-m-d');

        if (! $invoices = Cache::get('monthly-invoices')) {
            $invoices = $this->api->invoices($from, $to);
            Cache::put('monthly-invoices', $invoices, 1440 * 3);
        }

        $settings = $this->api->settings();
        $monthly = $this->api->monthlyTotals( $invoices );

        return view('home.dashboard', [
            'invoices'  => $invoices,
            'totals'    => $this->api->totals($invoices),
            'settings'  => $settings,
            'monthly'   => array_reverse($monthly),
        ]);
    }

} // END class