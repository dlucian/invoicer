<?php


namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Validator;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = \App\Models\Invoice::all();
        return response()->json($invoices);
    }

    public function get($invoiceId)
    {
        //TODO: implement me
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seller_name'   => 'required',
            'seller_info'   => 'required',
            'buyer_name'    => 'required',
            'buyer_info'    => 'required',
            'vat_percent'   => 'required',
            'products'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'code' => 400, 'data' => $validator->errors()], 400);
        }

        $invoice = Invoice::create($request->all());

        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->toArray() ]);
    }

    public function update($invoiceId)
    {
        //TODO: implement me
    }

    public function delete($invoiceId)
    {
        //TODO: implement me
    }

}