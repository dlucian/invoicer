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

    public function invoices( $from, $to )
    {
        $invoices = $this->call('GET', '?created_after=' . $from . '&created_before=' . $to );
        foreach ($invoices as & $invoice) {
            $invoice = $this->unpackProducts($invoice);
        }
        return $invoices;
    }

    public function invoice($invoiceId)
    {
        $invoice = $this->call('GET', sprintf('/%s', $invoiceId) );
        return $this->unpackProducts($invoice);
    }

    public function updateInvoice( $invoiceId, $invoiceData )
    {
        if (!empty($invoiceId)) {
            $invoiceData['invoice'] = $invoiceId;
            $invoiceData['products'] = json_encode($invoiceData['products']);
            return $this->call('PUT', sprintf('/%s', $invoiceId), $invoiceData);
        } else {
            $invoiceData['products'] = json_encode($invoiceData['products']);
            return $this->call('POST', '', $invoiceData);
        }
    }

    public function delete( $invoiceId )
    {
        return $this->call('DELETE', sprintf('/%s', $invoiceId));
    }

    public function domestic( $invoiceId )
    {
        return $this->retrievePdf( sprintf('/%s?pdf=domestic', $invoiceId ) );
    }

    public function foreign( $invoiceId )
    {
        return $this->retrievePdf( sprintf('/%s?pdf=foreign', $invoiceId ) );
    }

    public function totals( $invoices )
    {
        $total = ['foreign' => 0, 'domestic' => 0];
        foreach ($invoices as $invoice) {
            $total['foreign'] += !empty($invoice['subtotal_foreign']) ? $invoice['subtotal_foreign'] : 0;
            $total['domestic'] += $invoice['subtotal_domestic'];
        }
        return $total;
    }

    public function monthlyTotals( $invoices )
    {
        $monthly = [];
        foreach( $invoices as $invoice) {
            $invoiceMonth = date('Y-m',strtotime($invoice['issued_on']));
            if (empty($monthly[$invoiceMonth]))
                $monthly[$invoiceMonth] = 0;
            $monthly[$invoiceMonth] += $invoice['subtotal_domestic'];
        }
        return $monthly;
    }

    protected function unpackProducts($invoice)
    {
        $this->settings();
        if (!is_array($invoice['products']))
            $invoice['products'] = json_decode($invoice['products'], true);
        $subTotalForeign = 0;
        $subTotalDomestic = 0;
        $baseCurrency = '';
        foreach ($invoice['products'] as $product) {
            if (!empty($product['price_domestic'])) {
                $subTotalForeign += $product['price'] * $product['quantity'];
                $subTotalDomestic += $product['price_domestic'] * $product['quantity'];
                $baseCurrency = $product['currency'];
            } else {
                $subTotalDomestic += $product['price'] * $product['quantity'];
                $baseCurrency = $this->settings['domestic_currency'];
            }
        }

        $invoice['base_currency'] = $baseCurrency;

        // subtotals
        $invoice['subtotal_domestic'] = $subTotalDomestic;
        $invoice['subtotal_foreign'] = $subTotalForeign;

        // VAT
        $invoice['vat_value_domestic'] = 0;
        $invoice['vat_value_foreign'] = 0;
        if ($invoice['vat_percent'] > 0) {
            $invoice['vat_value_domestic'] = round($subTotalDomestic / (100/$invoice['vat_percent']), $this->settings['decimals']);
            if (!empty($subTotalForeign))
                $invoice['vat_value_foreign'] = round($subTotalForeign / (100/$invoice['vat_percent']), $this->settings['decimals']);
        }
        return $invoice;
    }

    protected function retrievePdf( $resource )
    {
        if (strpos($resource,'?'))
            $connectString = '&';
        else
            $connectString = '?';

        $client = new Client();
        $res = $client->request('GET', $this->apiEndpoint . '/v1/invoice' . $resource . $connectString . 'key=' . $this->apiKey);

        if ($res->getStatusCode() != 200)
            throw new Exception('Error requesting data.');
        if (!in_array('application/pdf', $res->getHeader('content-type')))
            throw new Exception('Invalid response content type.');

        return $res->getBody()->getContents();
    }

    protected function call( $action = 'GET', $resource = '', $data = array() )
    {
        if (strpos($resource,'?')!==false)
            $connectString = '&';
        else
            $connectString = '?';

        $client = new Client();
        $res = $client->request($action, $this->apiEndpoint . '/v1/invoice' . $resource . $connectString . 'key=' . $this->apiKey, ['form_params' => $data]);

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
        $res = $client->request($action, $this->apiEndpoint . '/v1/setting' . $resource . '?key=' . $this->apiKey, ['form_params' => $data]);
        if ($res->getStatusCode() != 200)
            throw new Exception('Error requesting data.');
        if (!in_array('application/json', $res->getHeader('content-type')))
            throw new Exception('Invalid response content type.');
        return json_decode($res->getBody()->getContents(), true)['data'];
    }

    public function settings()
    {
        if (empty($this->settings))
            $this->settings = $this->callSetting();
        ksort($this->settings);
        return $this->settings;
    }

    public function updateSetting( $settingName, $settingValue )
    {
        return $this->callSetting('PUT', sprintf('/%s', $settingName), ['value' => $settingValue]);
    }

    public function deleteSetting( $settingName )
    {
        return $this->callSetting('DELETE', sprintf('/%s', $settingName));
    }

} // END class