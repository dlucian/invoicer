<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class InvoiceApiTest extends TestCase
{

    public function testEmptyInvoices_shouldReturnEmptyJson()
    {
        $this->get('/v1/invoice')
            ->receiveJson();
    }

}
