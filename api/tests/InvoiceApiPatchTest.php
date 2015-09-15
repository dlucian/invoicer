<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiPatchTest extends InvoiceApiHelper
{
    public function testPatchOnInexistingInvoice_shouldReturnBadRequest()
    {
        $this->patch('/v1/invoice/' . uniqid(), [] )->seeJsonContains(['status' => 'fail', 'code' => 404]);
    }

   public function testPatchWithOneField_shouldUpdateTheField()
    {
        $this->simpleSettings();
        $bogusInvoice = $this->bogusInvoiceInfo();
        $originalResponse = $this->saveInvoice( $bogusInvoice );
        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);

        $this->patch('/v1/invoice/' . $originalResponse['invoice'], ['seller_name' => 'Singlepatched John'] )->seeJsonContains(['status' => 'success']);

        $newInvoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);
        $this->assertEquals('Singlepatched John', $newInvoice['seller_name']);
    }

    public function testPatchWithNonNumericVAT_shouldReturnBadRequest()
    {
        $this->simpleSettings();
        $bogusInvoice = $this->bogusInvoiceInfo();
        $originalResponse = $this->saveInvoice( $bogusInvoice );
        $invoice = $this->getInvoice('/v1/invoice/' . $originalResponse['invoice']);

        $this->patch('/v1/invoice/' . $originalResponse['invoice'], ['vat_percent' => 'junkware'] )->seeJsonContains(['status' => 'fail', 'code' => 400]);
    }


}