<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Setting;

class InvoiceApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testEmptyInvoices_shouldReturnEmptyJson()
    {
        $this->get('/v1/invoice')
            ->receiveJson();
    }

    public function testAddInvoiceWithoutBuyer_shouldReturnBadRequest() {
        $this->post('/v1/invoice', [
           'vat_percent' => 4
        ])->seeJsonContains(['status' => 'fail']);
    }

    public function testAddInvoiceWithoutSettings_shouldReturnFirstInvoice()
    {
        $this->post('/v1/invoice', [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name'  => 'Loathin Loather',
            'buyer_info'  => '123 Zcme Street',
            'vat_percent' => 20,
            'products'    => json_encode(['test' => 'data']),
        ])->seeJsonContains(['status' => 'success','invoice' => '1']);
    }

    public function testAddInvoiceWithDesignatedId_shouldReturnCorrectInvoice()
    {
        Setting::setByName('next_invoice', 15);
        Setting::setByName('invoice_prepend', 'TST');

        $this->post('/v1/invoice', [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name'  => 'Loathin Loather',
            'buyer_info'  => '123 Zcme Street',
            'vat_percent' => 20,
            'products'    => json_encode(['test' => 'data']),
        ])->seeJsonContains(['status' => 'success','invoice' => 'TST15']);
    }

    public function testConsecutiveAddInvoice_shouldReturnConsecutiveNumbers()
    {
        Setting::setByName('next_invoice', 28);
        Setting::setByName('invoice_prepend', 'TSX');

        $this->post('/v1/invoice', [
            'seller_name' => 'Testone Testovici',
            'seller_info' => '321 Wadyia Street',
            'buyer_name'  => 'Loathin Loather',
            'buyer_info'  => '123 Zcme Street',
            'vat_percent' => 20,
            'products'    => json_encode(['test' => 'data']),
        ])->seeJsonContains(['status' => 'success','invoice' => 'TSX28']);

        $this->post('/v1/invoice', [
            'seller_name' => 'Testone Testovici 2',
            'seller_info' => '3210 Wadyia Street',
            'buyer_name'  => 'Loathin Lither',
            'buyer_info'  => '333 Zcme Street',
            'vat_percent' => 25,
            'products'    => json_encode(['test' => 'data']),
        ])->seeJsonContains(['status' => 'success','invoice' => 'TSX29']);
    }

    public function testAddInvoiceWithLongDigits_shouldReturnCorrectInvoice()
    {
        Setting::setByName('next_invoice', 39);
        Setting::setByName('invoice_prepend', 'TSZ');
        Setting::setByName('invoice_digits', '10');

        $this->post('/v1/invoice', [
            'seller_name' => 'Testone Testovici 2',
            'seller_info' => '3210 Wadyia Street',
            'buyer_name'  => 'Loathin Lither',
            'buyer_info'  => '333 Zcme Street',
            'vat_percent' => 25,
            'products'    => json_encode(['test' => 'data']),
        ])->seeJsonContains(['status' => 'success','invoice' => 'TSZ0000000039']);
    }

}
