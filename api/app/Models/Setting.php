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

    public static function getByName($name, $default = false)
    {
        $setting = self::where('name',$name)->first();
        if (!empty($setting->id)) // found setting
            return $setting->value;
        return $default;
    }

    public static function setByName($name, $value)
    {
        $setting = self::where('name',$name)->first();
        if (!empty($setting->name)) {
            $setting->value = $value;
        } else {
            $setting = Setting::create(['name' => $name, 'value' => $value]);
        }
        $setting->save();
        return $setting;
    }

}