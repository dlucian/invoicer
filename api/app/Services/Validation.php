<?php
/**
 * Validation Service, for custom validations
 */

namespace App\Services;

use Illuminate\Validation\Validator;

class Validation extends Validator
{
    public function validateJson($attribute, $value, $parameters)
    {
        return !is_null(json_decode($value));
    }
}