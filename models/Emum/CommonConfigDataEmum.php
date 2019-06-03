<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-30
 * Time: 12:01
 */

namespace app\models\Emum;


class CommonConfigDataEmum
{
    public static $configLevel = [
        'private' => 1,
        'public'  => 2
    ];

    public static $valueType = [
        'string' => 1,
        'Json'   => 2
    ];
}