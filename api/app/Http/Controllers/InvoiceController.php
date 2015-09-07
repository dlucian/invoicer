<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Setting;
use Validator;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
    }

    public function get($invoiceId)
    {
        //TODO: implement me
    }

    public function create(Request $request)
    {

        $inputRules = [
            'buyer_name'    => 'required',
            'buyer_info'    => 'required',
            'vat_percent'   => 'required|numeric',
            'products'      => 'required|json',
        ];

        if (Setting::getByName('seller_name') === false || Setting::getByName('seller_info') === false) {
            $inputRules += ['seller_name' => 'required', 'seller_info' => 'required'];
        }

        $validator = Validator::make($request->all(), $inputRules);

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