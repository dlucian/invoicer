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

        parent::save();
    }

}