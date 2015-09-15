<?php
/**
 * Created by PhpStorm.
 * User: lucian
 * Date: 15/09/15
 * Time: 22:41
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Validator;


class SettingController extends Controller {

    public function index()
    {
        $settings = Setting::all();
        $outputArray = array();
        foreach ($settings as $setting)
            $outputArray[ $setting->name ] = $setting->value;

        return response()->json(['status' => 'success', 'code' => 0, 'data' => $outputArray ]);
    }

    public function get( $id )
    {
        return response()->json(['status' => 'success', 'code' => 0, 'data' => ['value' => Setting::getByName($id)] ]);
    }

    public function update( Request $request, $id )
    {
        Setting::setByName($id, $request->input('value'));
        return response()->json(['status' => 'success', 'code' => 0, 'data' => $request->input('value') ]);
    }

    public function delete( $id )
    {
        Setting::where('name', $id)->delete();
        return response()->json(['status' => 'success', 'code' => 0, 'data' => '' ]);
    }

}