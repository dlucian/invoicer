<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [ 'name', 'value' ];

    private static $rules = [
        'name'      => 'required',
        'value'     => 'required',
    ];

    public static function rules()
    {
        return self::$rules;
    }

}