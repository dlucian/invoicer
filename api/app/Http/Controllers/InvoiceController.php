<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Setting;
use App\Services\PdfInvoiceGenerator;
use Validator;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $invoices = Invoice::allBetween(
            $request->input('created_after', '2000-01-01'), $request->input('created_before', date('Y-m-d'))
        );
        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoices ]);
    }

    public function get(Request $request, $invoiceId)
    {
        $invoice = Invoice::retrieve(urldecode($invoiceId));
        if (empty($invoice))
            return response()->json(['status' => 'fail', 'code' => 404, 'message' => "Invoice $invoiceId not found."], 404);

        if (empty($request->input('pdf'))) {
            return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->attachExchangeInfo()->toArray() ]);
        } else {

            switch ($request->input('pdf')) {
                case 'domestic':
                    return response( PdfInvoiceGenerator::generateDomestic( $invoice ), 200, [
                        'Content-Type'  => 'application/pdf',
                        'Cache-Control' => 'private, must-revalidate, post-check=0, pre-check=0, max-age=1',
                        'Pragma'        => 'public',
                        'Expires'       => 'Mon, 21 Nov 1983 05:00:00 GMT',
                        'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
                        'Content-Disposition' => sprintf('inline; filename="%s-domestic.pdf"', $invoiceId)
                    ]);
                    break;
                case 'foreign':
                    return response( PdfInvoiceGenerator::generateForeign( $invoice ), 200, [
                        'Content-Type'  => 'application/pdf',
                        'Cache-Control' => 'private, must-revalidate, post-check=0, pre-check=0, max-age=1',
                        'Pragma'        => 'public',
                        'Expires'       => 'Mon, 21 Nov 1983 05:00:00 GMT',
                        'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
                        'Content-Disposition' => sprintf('inline; filename="%s-foreign.pdf"', $invoiceId)
                    ]);
                    break;
                default:
                    throw new \Exception('Invalid PDF invoice type ' . $request->input('pdf'));
            }
        }
    }

    public function create(Request $request)
    {

        $inputRules = [
            'buyer_name'    => 'required',
            'buyer_info'    => 'required',
            'vat_percent'   => 'required|numeric',
            'products'      => 'required|validJsonProducts',
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