<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Setting;

class InvoiceApiHelper extends TestCase {
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->createApplication();
    }

    protected function bogusInvoiceInfo()
    {
        return [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name' => 'Loathin Loather',
            'buyer_info' => '123 Zcme Street',
            'vat_percent' => 20,
            'products' => json_encode(array(
                ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'RON'],
                ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'RON']
            )),
            'extra' => 'This is some extra information',
        ];
    }

    protected function simpleSettings()
    {
        Setting::setByName('domestic_currency', 'RON');
        Setting::setByName('foreign_currency', 'USD');
    }

    protected function getInvoice( $resource )
    {
        $retrieved = $this->get($resource)->seeJsonContains(['status' => 'success']);
        $retrievedJson = json_decode($retrieved->response->content(), true);
        return $retrievedJson['data'];
    }

    protected function saveInvoice( Array $invoice, $expectedStatus = 'success' )
    {
        $postResult = $this->post('/v1/invoice', $invoice )
            ->seeJsonContains(['status' => $expectedStatus]);
        $response = json_decode($postResult->response->content(), true);
        return $response['data'];
    }
}