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
            $request->get('created_after', '2000-01-01'), $request->get('created_before', date('Y-m-d')), $request->get('search', '')
        );
        foreach ($invoices as & $invoice)
            $invoice->attachExchangeInfo();
        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoices ]);
    }

    public function get(Request $request, $invoiceId)
    {
        $invoice = Invoice::retrieve(urldecode($invoiceId));
        if (empty($invoice))
            return response()->json(['status' => 'fail', 'code' => 404, 'message' => "Invoice $invoiceId not found."]);

        if (empty($request->input('pdf'))) {
            return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->attachExchangeInfo()->toArray() ]);
        } else {

            switch ($request->input('pdf')) {
                case 'domestic':
                    return response( PdfInvoiceGenerator::generateDomestic( $invoice->attachExchangeInfo() ), 200, [
                        'Content-Type'  => 'application/pdf',
                        'Cache-Control' => 'private, must-revalidate, post-check=0, pre-check=0, max-age=1',
                        'Pragma'        => 'public',
                        'Expires'       => 'Mon, 21 Nov 1983 05:00:00 GMT',
                        'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
                        'Content-Disposition' => sprintf('inline; filename="%s-domestic.pdf"', $invoiceId)
                    ]);
                    break;
                case 'foreign':
                    return response( PdfInvoiceGenerator::generateForeign( $invoice->attachExchangeInfo() ), 200, [
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
        $inputRules = $this->getInputRules();
        $validator = Validator::make($request->all(), $inputRules);
        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'code' => 400, 'data' => $validator->errors()]);
        }

        $invoice = Invoice::create($request->all());

        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->toArray() ]);
    }

    public function update(Request $request, $invoiceId)
    {
        $invoice = Invoice::retrieve(urldecode($invoiceId));

        if (empty($invoice['invoice']))
            return response()->json(['status' => 'fail', 'code' => 404, 'message' => "Invoice $invoiceId not found."]);

        $inputRules = $this->getInputRules();
        $validator = Validator::make($request->all(), $inputRules);
        if ($validator->fails())
            return response()->json(['status' => 'fail', 'code' => 400, 'data' => $validator->errors()]);

        $updatedInvoice = $invoice->getAttributes();
        foreach ($updatedInvoice as $attribute => $value)
            $updatedInvoice[$attribute] = $request->input($attribute);

        unset($updatedInvoice['invoice']);
        unset($updatedInvoice['id']);
        if (!$invoice->update($updatedInvoice))
            return response()->json(['status' => 'error', 'code' => 500, 'message' => "Could not update resource." ]);

        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->toArray() ]);
    }

    public function patch(Request $request, $invoiceId)
    {
        $invoice = Invoice::retrieve(urldecode($invoiceId));

        if (empty($invoice['invoice']))
            return response()->json(['status' => 'fail', 'code' => 404, 'message' => "Invoice $invoiceId not found."]);

        $inputRules = [
            'vat_percent'   => 'numeric',
            'products'      => 'validJsonProducts'
        ];

        $validator = Validator::make($request->all(), $inputRules);
        if ($validator->fails())
            return response()->json(['status' => 'fail', 'code' => 400, 'data' => $validator->errors()]);

        if (!$invoice->update($request->all()))
            return response()->json(['status' => 'error', 'code' => 500, 'message' => "Could not update resource." ]);

        return response()->json(['status' => 'success', 'code' => 0, 'data' => $invoice->toArray() ]);
    }

    public function delete($invoiceId)
    {
        $invoice = Invoice::retrieve(urldecode($invoiceId));

        if (empty($invoice['invoice']))
            return response()->json(['status' => 'fail', 'code' => 404, 'message' => "Invoice $invoiceId not found."]);

        $invoice->delete();
        return response()->json(['status' => 'success', 'code' => 0, 'data' => '' ]);
    }

    protected function getInputRules()
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
        return $inputRules;
    }
}
