<?php
/**
 * Validation Service, for custom validations
 */

namespace App\Services;

use Illuminate\Validation\Validator;
use App\Models\Setting;

class Validation extends Validator
{
    public function validateJson($attribute, $value)
    {
        return !is_null(json_decode($value));
    }

    /**
     * Returns false if $value isn't a valid JSON array with an array of products, each of them having
     * all the required fields that correctly describe a product.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateValidJsonProducts($attribute, $value, $parameters)
    {
        $products = json_decode($value, true);
        if (is_null($products) or !is_array($products))
            return false;
        $currencies = $this->getCurrencies();
        foreach ($products as $product) {
            if (empty($product['description']) or empty($product['quantity']) or empty($product['price']) or empty($product['currency']))
                return false; // missing fields
            if (!empty($currencies) && !in_array($product['currency'], $currencies))
                return false; // unknown currency
        }

        return true;
    }

    private function getCurrencies()
    {
        $currencies = [];
        if (!empty($domestic = Setting::getByName('domestic_currency')))
            $currencies[] = $domestic;
        if (!empty($foreign = Setting::getByName('foreign_currency')))
            $currencies[] = $foreign;
        return $currencies;
    }

}