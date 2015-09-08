<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

class Currency extends Model
{
    protected $table = 'currency';

    public $timestamps = false;

    protected $fillable = [ 'currency', 'date', 'rate' ];

    private static $rules = [
        'currency'      => 'required',
        'date'     => 'required',
        'rate'     => 'required|numeric',
    ];

    public static function rules()
    {
        return self::$rules;
    }

    public static function retrieve( $currency, $date = '' )
    {
        if (empty($date))
            $date = date('Y-m-d');

        $currency = self::where('currency', $currency)->where('date', $date)->first();
        if (!empty($currency->id)) // found conversion
            return $currency->rate;
        return false;
    }

    public static function persist( $currency, $date, $rate )
    {
        self::deleteByCurrencyAndDate($currency, $date);
        $newCurrency = new Currency();
        $newCurrency->currency = strtoupper($currency);
        $newCurrency->date = date('Y-m-d', strtotime($date));
        $newCurrency->rate = $rate;
        $newCurrency->save();
        return $newCurrency;
    }

    public static function deleteByCurrencyAndDate( $currency, $date )
    {
        $existing = self::where('currency', $currency)->where('date', $date)->get();
        foreach ($existing as $current)
            $current->delete();
    }

}