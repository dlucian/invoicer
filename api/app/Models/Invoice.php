<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

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

    public static function rules()
    {
        return self::$rules;
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