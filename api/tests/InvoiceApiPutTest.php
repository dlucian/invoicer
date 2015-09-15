<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiPutTest extends InvoiceApiHelper
{

    public function testPutInvoice_updatesTheInformationInTheDb()
    {
        $this->simpleSettings();
        $bogusInvoice = $this->bogusInvoiceInfo();
        $originalResponse = $this->saveInvoice( $bogusInvoice );
        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);

        $bogusInvoice['seller_name'] = 'The Updated Guy';
        $this->put('/v1/invoice/' . $originalResponse['invoice'], $bogusInvoice )->seeJsonContains(['status' => 'success']);

        $newInvoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);
        $this->assertEquals('The Updated Guy', $newInvoice['seller_name']);
    }

    public function testPutInexistingInvoice_returnsBadRequest()
    {
        $this->put('/v1/invoice/' . uniqid(), [] )->seeJsonContains(['status' => 'fail', 'code' => 404]);
    }

    public function testPutIncompleteInvoice_returnsValidationErrors()
    {
        $this->simpleSettings();
        $bogusInvoice = $this->bogusInvoiceInfo();
        $originalResponse = $this->saveInvoice( $bogusInvoice );
        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);

        $bogusInvoice['seller_name'] = 'The Updated Guy';
        $this->put('/v1/invoice/' . $originalResponse['invoice'], ['seller_name' => 'The Incomplete Acme Inc.'] )
            ->seeJsonContains(['status' => 'fail', 'code' => 400]);
    }

} // END class