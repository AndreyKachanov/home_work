<?php

namespace App\Services;

class CurrencyService
{
    const CURRENCY_CODE_USD = 'USD';
    const CURRENCY_CODE_EUR = 'EUR';

    /**
     * @param $data
     * @param bool $caseLower
     * @return array
     */
    public static function updateCurrencyRate($data, $caseLower = false)
    {

        if ($caseLower) {
            $data = self::changeKeyCaseMultidimensionArray($data, 'CASE_LOWER');
        }

        if (isset($data['last_update'])) {
            $data['last_update'] = date('Y-m-d');
        }

        foreach ($data['currency'] as $key => $item) {

            if($item['currencycode'] == self::CURRENCY_CODE_USD) {
                $data['currency'][$key]['rate'] = 50;
            }

            if($item['currencycode'] == self::CURRENCY_CODE_EUR) {
                $data['currency'][$key]['rate'] = 100;
            }

            if (isset($item['last_update'])) {
                $data['currency'][$key]['last_update'] = date('Y-m-d');
            }
        }

        if ($caseLower) {
            return self::changeKeyCaseMultidimensionArray($data, 'CASE_UPPER');
        }

        return $data;
    }

    /**Change key case for in a multidimensional array
     * @param $arr
     * @param $type (CASE_LOWER or CASE_UPPER)
     * @return array
     */
    public static function changeKeyCaseMultidimensionArray($arr, $type)
    {
        return array_map(function($item) use ($type) {
            if(is_array($item))
                $item = self::changeKeyCaseMultidimensionArray($item, $type);
            return $item;
        }, array_change_key_case($arr, ($type == 'CASE_LOWER') ? CASE_LOWER : CASE_UPPER));
    }
}