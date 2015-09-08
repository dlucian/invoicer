<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Setting;

class InvoiceApiGetOneTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createApplication();
    }

    private function bogusInvoiceInfo()
    {
        return [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name' => 'Loathin Loather',
            'buyer_info' => '123 Zcme Street',
            'vat_percent' => 20,
            'products' => json_encode(array(
                ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'USD'],
                ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'USD']
            )),
        ];
    }

    public function testAddInvoiceAndThenGet_shouldReturnItIntact()
    {
        Setting::setByName('domestic_currency', 'RON');
        Setting::setByName('foreign_currency', 'USD');
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


}