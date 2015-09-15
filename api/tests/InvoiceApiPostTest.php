<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Setting;

require_once( dirname(__FILE__) . '/InvoiceApiHelper.php' );

class InvoiceApiPostTest extends InvoiceApiHelper
{

    public function testAddInvoiceWithoutBuyer_shouldReturnBadRequest() {
        $invoice = $this->bogusInvoiceInfo();
        unset($invoice['buyer_name']);
        unset($invoice['buyer_info']);
        $this->post('/v1/invoice', $invoice)->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoiceWithoutSettings_shouldReturnFirstInvoice()
    {
        DB::table('settings')->truncate();

        $this->post('/v1/invoice', $this->bogusInvoiceInfo() )
            ->seeJsonContains(['status' => 'success','invoice' => '1']);
    }

    public function testAddInvoiceWithDesignatedId_shouldReturnCorrectInvoice()
    {
        Setting::setByName('next_invoice', 15);
        Setting::setByName('invoice_prepend', 'TST');

        $this->post('/v1/invoice', $this->bogusInvoiceInfo() )
            ->seeJsonContains(['status' => 'success','invoice' => 'TST15']);
    }

    public function testConsecutiveAddInvoice_shouldReturnConsecutiveNumbers()
    {
        Setting::setByName('next_invoice', 28);
        Setting::setByName('invoice_prepend', 'TSX');

        $this->post('/v1/invoice', $this->bogusInvoiceInfo() )
            ->seeJsonContains(['status' => 'success','invoice' => 'TSX28']);

        $this->post('/v1/invoice', $this->bogusInvoiceInfo() )
            ->seeJsonContains(['status' => 'success','invoice' => 'TSX29']);
    }

    public function testAddInvoiceWithLongDigits_shouldReturnCorrectInvoice()
    {
        Setting::setByName('next_invoice', 39);
        Setting::setByName('invoice_prepend', 'TSZ');
        Setting::setByName('invoice_digits', '10');

        $this->post('/v1/invoice', $this->bogusInvoiceInfo() )
            ->seeJsonContains(['status' => 'success','invoice' => 'TSZ0000000039']);
    }

    public function testAddInvoiceWithoutSettingsAndSeller_shouldReturnBadRequest()
    {
        DB::table('settings')->truncate();

        $bogusInfo = $this->bogusInvoiceInfo();
        unset($bogusInfo['seller_name']);
        unset($bogusInfo['seller_info']);

        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoiceWithInvalidProductsJson_shouldReturnBadRequest()
    {
        $bogusInfo = $this->bogusInvoiceInfo();
        $bogusInfo['products'] = 'vasile';

        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoiceWithIncompleteProducts_shouldReturnBadRequest()
    {
        $bogusInfo = $this->bogusInvoiceInfo();
        $bogusInfo['products'] = json_encode(array(['description' => 'Some product', 'quantity' => 7]));

        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoiceWithValidProducts_shouldWork()
    {
        Setting::setByName('domestic_currency', 'RON');
        Setting::setByName('foreign_currency', 'USD');

        $bogusInfo = $this->bogusInvoiceInfo();

        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'success']);
    }

    public function testAddInvoiceWithUnknownCurrency_shouldReturnBadRequest()
    {
        Setting::setByName('domestic_currency', 'RON');
        Setting::setByName('foreign_currency', 'USD');

        $bogusInfo = $this->bogusInvoiceInfo();
        $bogusInfo['products'] = json_encode(array(
            ['description' => 'Ice Cream', 'quantity' => 2, 'price' => 3.5, 'currency' => 'MKD'],
            ['description' => 'Peanut Butter', 'quantity' => 1, 'price' => 15.0, 'currency' => 'GBP']
        ));
        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'fail']);
    }

    public function testNoNumericVat_shouldReturnBadRequest()
    {
        $bogusInfo = $this->bogusInvoiceInfo();
        $bogusInfo['vat_percent'] = 'vasile';

        $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoice_shouldReturnItIntact()
    {
        Setting::setByName('domestic_currency', 'RON');
        Setting::setByName('foreign_currency', 'USD');

        $bogusInfo = $this->bogusInvoiceInfo();

        $output = $this->post('/v1/invoice', $bogusInfo )
            ->seeJsonContains(['status' => 'success']);

        $saved = json_decode($output->response->content(), true);
        $savedInvoice = $saved['data'];

        $this->assertEquals( $bogusInfo['seller_name'], $savedInvoice['seller_name'] );
        $this->assertEquals( $bogusInfo['seller_info'], $savedInvoice['seller_info'] );
        $this->assertEquals( $bogusInfo['buyer_name'], $savedInvoice['buyer_name'] );
        $this->assertEquals( $bogusInfo['buyer_info'], $savedInvoice['buyer_info'] );
        $this->assertEquals( $bogusInfo['vat_percent'], $savedInvoice['vat_percent'] );
        $this->assertEquals( $bogusInfo['products'], $savedInvoice['products'] );
    }

}
