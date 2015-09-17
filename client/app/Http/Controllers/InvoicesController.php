<?php

namespace App\Http\Controllers;

use Validator;
use App\InvoicerApi;
use Illuminate\Http\Request;

class InvoicesController extends Controller {

    protected $api = null;

    public function __construct()
    {
        $this->api = $api = new InvoicerApi(env('INVOICER_ENDPOINT'), env('INVOICER_KEY'));
    }

    public function index()
    {
        return view('invoices.list', ['invoices' => $this->api->invoices() ]);
    }

    public function view( $invoiceId )
    {
        return view('invoices.view', ['invoice' => $this->api->invoice( $invoiceId ), 'settings' => $this->api->settings() ]);
    }

    public function update( Request $request, $invoiceId )
    {
        $invoice = $this->api->invoice( $invoiceId );
        $settings = $this->api->settings();

        return view('invoices.update', ['invoice' => $invoice, 'settings' => $settings ]);
    }

    public function create( Request $request )
    {
        $settings = $this->api->settings();

        return view('invoices.update', [ 'settings' => $settings ]);
    }

    public function duplicate( Request $request, $invoiceId )
    {
        $invoice = $this->api->invoice( $invoiceId );
        unset( $invoice['invoice'] );
        unset( $invoice['id'] );

        $settings = $this->api->settings();

        return view('invoices.update', ['invoice' => $invoice, 'settings' => $settings ]);
    }

    public function store( Request $request, $invoiceId )
    {
        $validator = Validator::make($request->all(), [
            'issued_on'     => 'required|date',
            'seller_name'   => 'required',
            'buyer_name'    => 'required',
            'seller_info'   => 'required',
            'buyer_info'    => 'required',
            'issuer_info'   => 'required',
            'receiver_info' => 'required',

            'description.0' => 'required',
            'quantity.0'    => 'required|numeric',
            'price.0'       => 'required|numeric',
            'currency.0'    => 'required|max:3|min:3',
        ]);

        if ($validator->fails())
            return redirect(route( $invoiceId ? 'invoice-update' : 'invoice-create', $invoiceId))
                ->withErrors($validator)
                ->withInput();

        $updateResult = $this->api->updateInvoice( $invoiceId, $this->requestToInvoice($request->all()) );

        return redirect(route('invoice-view', $updateResult['invoice']));
    }

    public function delete( $invoiceId )
    {
        $invoice = $this->api->invoice( $invoiceId );
        if (empty($invoice['invoice']))
            die('Could not find invoice!');

        $this->api->delete( $invoiceId );

        return redirect(route('invoices-list'));
    }

    protected function requestToInvoice( $requestData )
    {
        $products = [];
        foreach($requestData['description'] as $key => $value)
            if (!empty($requestData['quantity'][$key]) && !empty($requestData['price'][$key]) && !empty($requestData['currency'][$key])) {
                $products[] = [
                    'description'   => $requestData['description'][$key],
                    'quantity'      => $requestData['quantity'][$key],
                    'price'         => $requestData['price'][$key],
                    'currency'      => $requestData['currency'][$key],
                ];
            }
        $requestData['products'] = $products;
        unset($requestData['description']);
        unset($requestData['quantity']);
        unset($requestData['price']);
        unset($requestData['currency']);
        unset($requestData['_token']);
        return $requestData;
    }
} // END class