<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-07-08
 * Time: 16:09
 */

namespace app\models\Mail;


class MailMessageStruct
{
    public static $mailMessages=[];

    public static function pushMailMessage($message)
    {
        array_push(static::$mailMessages,$message);
    }

    public static function unshiftMailMessage($message)
    {
        array_unshift(static::$mailMessages,$message);
    }
}