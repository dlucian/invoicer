<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [ 'name' => 'next_invoice',     'value' => '207' ],
            [ 'name' => 'invoice_prepend',  'value' => 'CVI F' ],
            [ 'name' => 'invoice_digits',   'value' => '3' ],
            [ 'name' => 'seller_name',      'value' => 'ACME Inc.' ],
            [ 'name' => 'seller_info',      'value' => "J11/2222/3333\nRO99995555\n17 Noname Str.\nTimisoara, Timis\nRomania" ],
            [ 'name' => 'issuer',           'value' => 'Jhon Travolta' ],
            [ 'name' => 'vat_percent',       'value' => '24' ],
            [ 'name' => 'branding',    'value' => 'Invocerware' ],
            [ 'name' => 'decimals',         'value' => '2' ],
            [ 'name' => 'domestic_currency', 'value' => 'RON' ],
            [ 'name' => 'foreign_currency', 'value' => 'RON' ],
            [ 'name' => 'issuer_info', 'value' => '' ],
            [ 'name' => 'branding_label', 'value' => '' ],

        ]);
    }
}
