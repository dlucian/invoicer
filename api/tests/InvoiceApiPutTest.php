<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiPutTest extends InvoiceApiHelper
{

    public function testPutInvoice_updatesTheInformationInTheDb()
    {
        $bogusInvoice = $this->bogusInvoiceInfo();
        //$this->post('/v1/invoice', $bogusInvoice )->seeJsonContains(['status' => 'success']);
    }

} // END class