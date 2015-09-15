<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiDeleteTest extends InvoiceApiHelper
{
    public function testDeleteNonExistingInvoice_shouldReturn404NotFound()
    {
        $this->delete('/v1/invoice/' . uniqid(), [] )->seeJsonContains(['status' => 'fail', 'code' => 404]);
    }

    public function testDeleteExistingInvoice_wouldDeleteIt()
    {
        $this->simpleSettings();
        $bogusInvoice = $this->bogusInvoiceInfo();
        $originalResponse = $this->saveInvoice( $bogusInvoice );

        $this->delete('/v1/invoice/' . $originalResponse['invoice'])->seeJsonContains(['status' => 'success']);

        $this->get('/v1/invoice/' . $originalResponse['invoice'])->seeJsonContains(['status' => 'fail', 'code' => 404]);
    }
}