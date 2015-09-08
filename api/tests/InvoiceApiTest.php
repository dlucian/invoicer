<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Setting;

class InvoiceApiTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createApplication();
    }

    private function bogusInvoiceInfo()
    {
        return [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name'  => 'Loathin Loather',
            'buyer_info'  => '123 Zcme Street',
            'vat_percent' => 20,
            'products'    => json_encode(array(
                ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'USD'],
                ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'USD']
            )),
        ];
    }

    public function testEmptyInvoices_shouldReturnEmptyJson()
    {
        $this->get('/v1/invoice')
            ->receiveJson();
    }



}
