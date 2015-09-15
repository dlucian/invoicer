<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;
use App\Services\CurrencyConverter;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice', 'issued_on', 'seller_name', 'seller_info', 'buyer_name', 'buyer_info', 'vat_percent',
        'products', 'issuer_info', 'receiver_info', 'branding', 'extra'
    ];

    private static $rules = [
        'invoice'       => 'required|unique:invoices',
        'issued_on'     => 'required|date',
        'seller_name'   => 'required',
        'seller_info'   => 'required',
        'buyer_name'    => 'required',
        'buyer_info'    => 'required',
        'vat_percent'   => 'required',
        'products'      => 'required',
    ];

    private $exchangeRate = 0;
    private $foreignCurrency = '';
    private $productsList = [];

    public static function rules()
    {
        return self::$rules;
    }

    public function exchangeRate()
    {
        return $this->exchangeRate;
    }

    public function foreignCurrency()
    {
        return $this->foreignCurrency;
    }

    public function save(array $options = array())
    {
        // check if it has ID and issued_on, generate them otherwise
        if (empty($this->invoice))
            $this->setupNewInvoice();

        if (empty($this->issued_on))
            $this->issued_on = date('Y-m-d');

        $saved = parent::save();

        if ($saved)
            $this->prepareNextInvoice();
    }

    public static function allBetween( $dateStart = '', $dateStop = '' )
    {
        if (empty($dateStart))
            $dateStart = '2000-01-01';
        if (empty($dateStop))
            $dateStop = date('Y-m-d');
        return self::whereBetween('created_at',[$dateStart . ' 00:00:00', $dateStop . ' 23:59:59'])
            ->get();
    }

    public static function retrieve( $invoiceId )
    {
        $invoice = self::where('invoice','=', $invoiceId )->first();
        return $invoice;
    }

    public function attachExchangeInfo()
    {
        if ($this->isForeign()) {
            $this->exchangeRate = CurrencyConverter::getRate( $this->getForeignCurrency(), $this->issued_on );

            $products = $this->getProducts();
            foreach ($products as & $product) {
                if ($product['currency'] == $this->foreignCurrency)
                    $product['price_domestic'] = round( $this->exchangeRate * $product['price'], Setting::getByName('decimals') );
            }
            $this->products = json_encode($products);
        }
        return $this;
    }

    public function isForeign()
    {
        $this->foreignCurrency = $this->getForeignCurrency();

        if (!empty($this->foreignCurrency)) {
            $products = $this->getProducts();
            foreach ($products as $product) {
                if ($product['currency'] == $this->foreignCurrency)
                    return true;
            }
        }
        return false;
    }

    public function getProducts()
    {
        return json_decode($this->products, true);
    }

    public function getForeignCurrency()
    {
        if (empty($this->foreignCurrency))
            $this->foreignCurrency = Setting::getByName('foreign_currency');
        return $this->foreignCurrency;
    }

    public function toArray()
    {
        return parent::toArray() + ['exchange_rate' => $this->exchangeRate];
    }

    private function setupNewInvoice()
    {
        $nextInvoice = (int)Setting::getByName('next_invoice');
        if ($nextInvoice == 0)
            Setting::setByName('next_invoice', $nextInvoice = 1 );

        $invoiceFormat = sprintf( '%%s%%0%dd', Setting::getByName('invoice_digits') );
        $this->invoice = sprintf( $invoiceFormat,
            Setting::getByName('invoice_prepend'),
            $nextInvoice
        );
    }

    private function prepareNextInvoice()
    {
        Setting::setByName('next_invoice', (int)Setting::getByName('next_invoice') + 1 );
    }
}