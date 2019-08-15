<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-08-15
 * Time: 11:37
 */

namespace app\models;


class Helper
{
    public static function getCode($length = 15, $prefix = '')
    {
        list($mSec, $sec) = explode(' ', microtime()); //获取时间戳
        $mSecTime = intval($mSec * 1000000) + $sec * 1000000; //计算生成微妙级时间戳
        $timestampStr = substr($mSecTime, 1); //去除微妙级时间戳的最高位
        $timestampLen = strlen($timestampStr);//计算去除最高位后时间戳的长度
        if ($length > $timestampLen) { // 如果传入的长度大于 去除之后的长度 就在后面随机几位的数字
            $padLength = $length - $timestampLen;
            return $prefix . $timestampStr . str_pad(mt_rand(0, static::getMaxByLength($padLength)), $padLength, '0', STR_PAD_LEFT);
        } elseif ($length == $timestampLen) { //如果等于 就返回 微妙级时间戳
            return $prefix . $timestampStr;
        }
        throw new \Exception('传入最小位数为' . $timestampLen);
    }

    private static function getMaxByLength($len)
    {
        return pow(10, $len) - 1;
    }
}