<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiGetOneTest extends InvoiceApiHelper
{

    public function testAddInvoiceAndThenGet_shouldReturnItIntact()
    {
        $this->simpleSettings();
        $bogusInfo = $this->bogusInvoiceInfo();
        $output = $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'success']);
        $saved = json_decode($output->response->content(), true);

        $retrieved = $this->get('/v1/invoice/' . $saved['data']['invoice'])
            ->seeJsonContains(['status' => 'success']);
        $retrievedJson = json_decode($retrieved->response->content(), true);
        $retrievedInvoice = $retrievedJson['data'];

        $this->assertEquals( $bogusInfo['seller_name'], $retrievedInvoice['seller_name'] );
        $this->assertEquals( $bogusInfo['seller_info'], $retrievedInvoice['seller_info'] );
        $this->assertEquals( $bogusInfo['buyer_name'], $retrievedInvoice['buyer_name'] );
        $this->assertEquals( $bogusInfo['buyer_info'], $retrievedInvoice['buyer_info'] );
        $this->assertEquals( $bogusInfo['vat_percent'], $retrievedInvoice['vat_percent'] );
        $this->assertEquals( $bogusInfo['products'], $retrievedInvoice['products'] );
    }

    public function testGetNonexistingInvoice_shouldReturn404()
    {
        $this->get('/v1/invoice/THIS_CANT_EXIST')
            ->seeJsonContains(['status' => 'fail'])
            ->seeJsonContains(['code' => 404]);
    }

    public function testGetDomesticInvoice_shouldNotContainForeignInfo()
    {
        $this->simpleSettings(); // RON = domestic currency
        $originalInvoice = $this->bogusInvoiceInfo();
        $originalInvoice['products'] = json_encode(array(
            ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'RON'],
            ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'RON']
        ));
        $originalResponse = $this->saveInvoice( $originalInvoice );

        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);

        $this->assertTrue(empty($invoice['exchange_rate']));
        $products = json_decode($invoice['products'], true);
        foreach ($products as $product) {
            $this->assertTrue(empty($product['price_foreign']));
        }
    }

    public function testGetForeignInvoice_shouldIncludeExchangeRateAndForeignPrices()
    {
        $this->simpleSettings(); // RON = domestic currency
        $originalInvoice = $this->bogusInvoiceInfo();
        $originalInvoice['products'] = json_encode(array(
            ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'USD'],
            ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'USD']
        ));
        $originalResponse = $this->saveInvoice( $originalInvoice );

        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);
        $this->assertGreaterThan( 0, $invoice['exchange_rate'] );

        $products = json_decode($invoice['products'], true);
        foreach ($products as $product) {
            $this->assertGreaterThan( 0, $product['price_domestic']);
        }
    }

    public function testGetDomesticPdfInvoice_shouldDeliverAPdfFile()
    {
        $this->simpleSettings(); // RON = domestic currency
        $originalInvoice = $this->bogusInvoiceInfo();
        $originalInvoice['products'] = json_encode(array(
            ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'RON'],
            ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'RON']
        ));
        $originalResponse = $this->saveInvoice( $originalInvoice );

        $domesticPdfResponse = $this->get('/v1/invoice/' . $originalResponse['invoice'] . '?pdf=domestic');

        $this->assertEquals('application/pdf', $domesticPdfResponse->response->headers->get('Content-Type'));
    }

    public function testGetForeignPdfInvoice_shouldDeliverAPdfFile()
    {
        $this->simpleSettings(); // RON = domestic currency
        $originalInvoice = $this->bogusInvoiceInfo();
        $originalInvoice['products'] = json_encode(array(
            ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'RON'],
            ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'RON']
        ));
        $originalResponse = $this->saveInvoice( $originalInvoice );

        $domesticPdfResponse = $this->get('/v1/invoice/' . $originalResponse['invoice'] . '?pdf=foreign');

        $this->assertEquals('application/pdf', $domesticPdfResponse->response->headers->get('Content-Type'));
    }



}