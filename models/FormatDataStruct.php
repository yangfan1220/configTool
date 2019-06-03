<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-20
 * Time: 17:17
 */

namespace app\models;


class FormatDataStruct
{
    const SUCCESS_CODE = 0;
    const ERROR_CODE = 400;

    public static function failed(string $msg = 'failed', array $data = [], int $code = self::ERROR_CODE)
    {
        return static::success($data, $msg, $code);
    }

    public static function success($data = [], string $msg = 'success', int $code = self::SUCCESS_CODE)
    {
        $responseData = [
            'code' => (Int)$code,
            'message'  => (String)$msg,
            'data' => (array)$data
        ];
        return $responseData;
    }
}