<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Mockery\CountValidator\Exception;

class InvoicerApi {

    protected $apiEndpoint = '';
    protected $apiKey = '';

    protected $settings = array();

    public function __construct( $invoicerApiEndpoint, $invoicerApiKey )
    {
        $this->apiEndpoint = $invoicerApiEndpoint;
        $this->apiKey = $invoicerApiKey;
    }

    public function invoices()
    {
        return $this->call('GET');
    }

    public function invoice($invoiceId)
    {
        $invoice = $this->call('GET', sprintf('/%s', $invoiceId) );
//dd($invoice);
        return $this->unpackProducts($invoice);
    }

    public function settings()
    {
        if (empty($this->settings))
            $this->settings = $this->callSetting();
        return $this->settings;
    }

    public function updateInvoice( $invoiceId, $invoiceData )
    {
        $invoiceData['invoice'] = $invoiceId;
        $invoiceData['products'] = json_encode($invoiceData['products']);
        return $this->call('PUT', sprintf('/%s', $invoiceId), $invoiceData);
    }

    protected function unpackProducts($invoice)
    {
        if (!is_array($invoice['products']))
            $invoice['products'] = json_decode($invoice['products'], true);
        $subTotal = 0;
        $subTotalDomestic = 0;
        foreach ($invoice['products'] as $product) {
            $subTotal += $product['price'] * $product['quantity'];
            if (!empty($product['price_domestic']))
                $subTotalDomestic += $product['price_domestic'] * $product['quantity'];
        }

        // subtotals
        $invoice['subtotal'] = $subTotal;
        if (!empty($subTotalDomestic))
            $invoice['subtotal_domestic'] = $subTotalDomestic;

        // VAT
        $invoice['vat_value'] = $subTotal / (100/$invoice['vat_percent']);
        if (!empty($subTotalDomestic))
            $invoice['vat_domestic'] = $subTotalDomestic / (100/$invoice['vat_percent']);

        return $invoice;
    }

    protected function call( $action = 'GET', $resource = '', $data = array() )
    {
        $client = new Client();
        $res = $client->request($action, $this->apiEndpoint . '/v1/invoice' . $resource . '?key=' . $this->apiKey, ['form_params' => $data]);

        if ($res->getStatusCode() != 200)
            throw new Exception('Error requesting data.');
        if (!in_array('application/json', $res->getHeader('content-type')))
            throw new Exception('Invalid response content type.');

        $output = json_decode($res->getBody()->getContents(), true);
        if ($output['code'] != 0)
            throw new Exception('Request failed, code ' . $output['code'] . ': ' . print_r($output,1));
        return $output['data'];
    }

    protected function callSetting( $action = 'GET', $resource = '', $data = array() )
    {
        $client = new Client();
        $res = $client->request($action, $this->apiEndpoint . '/v1/setting' . $resource . '?key=' . $this->apiKey, $data);
        if ($res->getStatusCode() != 200)
            throw new Exception('Error requesting data.');
        if (!in_array('application/json', $res->getHeader('content-type')))
            throw new Exception('Invalid response content type.');

        return json_decode($res->getBody()->getContents(), true)['data'];
    }

} // END class