<?php

/**
 * Currency rate service container.
 * Feel free to replace this with whatever you need in your country.
 *
 * @uses http://openapi.ro/#exchange
 */

namespace App\Services;

use \GuzzleHttp\Client;
use \App\Models\Currency;

class CurrencyConverter
{

    private $date = '';
    private $foreignCurrency = '';

    public function __construct($date = '', $currency = 'USD')
    {
        if (empty($date))
            $date = date('Y-m-d');
        $this->date = $date;
        $this->foreignCurrency = $currency;
    }

    public function convert($foreignValue)
    {
        $exchangeRate = $this->retrieveRate();
        if ($exchangeRate === false)
            return $exchangeRate;

        return $exchangeRate * $foreignValue;
    }

    public static function toForeign($value, $currency, $date = '')
    {
        $currency = new CurrencyConverter($date, $currency);
        return $currency->convert($value);
    }

    public static function getRate($currency, $date)
    {
        $currency = new CurrencyConverter($date, $currency);
        return $currency->retrieveRate();
    }

    protected function retrieveRate()
    {
        if (($localRate = $this->retrieveLocal()) === false)
            return $this->retrieveRemote();
        return $localRate;
    }

    protected function retrieveLocal()
    {
        return Currency::retrieve($this->foreignCurrency, $this->date);
    }

    protected function retrieveRemote()
    {
        $client = new Client();
        $url = sprintf('http://api.openapi.ro/api/exchange/%s.json?date=%s', strtolower($this->foreignCurrency), $this->date);
        $res = $client->request(
            'GET',
            $url,
            ['headers' => ['x-api-key' => env('OPENAPI_KEY')]]
        );

        if ($res->getStatusCode() == 200) {
            $responseJson = (string)$res->getBody();
            $exchangeRate = json_decode($responseJson);
            if (!empty($exchangeRate->rate)) {
                $this->persistLocal($exchangeRate->rate);
                return $exchangeRate->rate;
            }
        }
        return false;
    }

    protected function persistLocal($exchangeRate)
    {
        Currency::persist($this->foreignCurrency, $this->date, $exchangeRate);
    }
}
